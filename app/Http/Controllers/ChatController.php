<?php

namespace App\Http\Controllers;

use App\Events\MessageDeleted;
use App\Events\MessageEdited;
use App\Events\MessageReactionAdded;
use App\Events\MessageReactionRemoved;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Models\ChatMessageReaction;
use App\Models\ChatNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function index(): Response
    {
        $user = auth()->user();

        $channels = $user->activeChatChannels()
            ->with(['users', 'latestMessage.user'])
            ->withCount([
                'messages as unread_count' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)
                        ->whereDoesntHave('reads', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                }
            ])
            ->latest('updated_at')
            ->get()
            ->map(function ($channel) use ($user) {
                return [
                    'id' => $channel->id,
                    'type' => $channel->type,
                    'name' => $channel->name ?? $channel->getOtherUser($user)?->name,
                    'avatar' => $channel->isDirect() ? $channel->getOtherUser($user)?->avatar : null,
                    'latest_message' => $channel->latestMessage->first(),
                    'unread_count' => $channel->unread_count,
                    'updated_at' => $channel->updated_at,
                    'users' => $channel->users->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'avatar' => $u->avatar,
                        'is_online' => $u->isOnline(),
                    ]),
                ];
            });

        // If user is admin or super admin, get all users in the system
        $allUsers = [];
        if ($user->hasRole(['super-admin', 'Super Admin', 'admin', 'Admin'])) {
            $allUsers = User::where('id', '!=', $user->id)
                ->select('id', 'name', 'email', 'avatar')
                ->orderBy('name')
                ->get()
                ->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'avatar' => $u->avatar,
                    ];
                });
        }

        return Inertia::render('Dashboard/Chat/Index', [
            'channels' => $channels,
            'totalUnread' => $user->unread_messages_count,
            'allUsers' => $allUsers,
            'isAdmin' => $user->hasRole(['super-admin', 'Super Admin', 'admin', 'Admin']),
        ]);
    }

    /**
     * Get all channels for the authenticated user
     */
    public function channels(): JsonResponse
    {
        $user = auth()->user();

        $channels = $user->activeChatChannels()
            ->with(['users', 'latestMessage.user'])
            ->withCount([
                'messages as unread_count' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)
                        ->whereDoesntHave('reads', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                }
            ])
            ->latest('updated_at')
            ->get();

        return response()->json([
            'channels' => $channels,
        ]);
    }

    /**
     * Create a new channel (direct or group)
     */
    public function createChannel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:direct,group',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
            'name' => 'required_if:type,group|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('chat.error_creating_channel'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $currentUser = auth()->user();
        $type = $request->type;
        $userIds = $request->user_ids;

        // Check permissions for each user
        foreach ($userIds as $userId) {
            $otherUser = User::find($userId);
            if (!$currentUser->canChatWith($otherUser)) {
                return response()->json([
                    'message' => __('chat.permission_denied'),
                ], 403);
            }
        }

        // For direct messages, check if channel already exists
        if ($type === 'direct' && count($userIds) === 1) {
            $existingChannel = $currentUser->activeChatChannels()
                ->direct()
                ->whereHas('users', function ($query) use ($userIds) {
                    $query->where('user_id', $userIds[0]);
                })
                ->first();

            if ($existingChannel) {
                return response()->json([
                    'channel' => $existingChannel->load('users'),
                    'message' => __('chat.channel_created'),
                ]);
            }
        }

        // Create the channel
        $channel = ChatChannel::create([
            'type' => $type,
            'name' => $request->name,
            'created_by' => $currentUser->id,
        ]);

        // Add creator to channel
        $channel->users()->attach($currentUser->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        // Add other users
        foreach ($userIds as $userId) {
            $channel->users()->attach($userId, [
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }

        return response()->json([
            'channel' => $channel->load('users'),
            'message' => __('chat.channel_created'),
        ], 201);
    }

    /**
     * Get messages for a specific channel
     */
    public function messages(Request $request, ChatChannel $channel): JsonResponse
    {
        $user = auth()->user();

        // Check if user is a member
        if (!$channel->hasMember($user)) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        // Check if user is blocked
        if ($channel->isUserBlocked($user)) {
            return response()->json([
                'message' => __('chat.blocked_message'),
            ], 403);
        }

        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $messages = $channel->messages()
            ->withDetails()
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'messages' => $messages->items(),
            'has_more' => $messages->hasMorePages(),
            'current_page' => $messages->currentPage(),
            'total' => $messages->total(),
        ]);
    }

    /**
     * Send a message in a channel
     */
    public function sendMessage(Request $request, ChatChannel $channel): JsonResponse
    {
        $user = auth()->user();

        // Check if user is a member
        if (!$channel->hasMember($user)) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        // Check if user is blocked
        if ($channel->isUserBlocked($user)) {
            return response()->json([
                'message' => __('chat.blocked_message'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file|string|max:5000',
            'file' => 'required_without:message|file|max:10240', // 10MB
            'reply_to_message_id' => 'nullable|exists:chat_messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('chat.error_sending_message'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $messageData = [
            'channel_id' => $channel->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->hasFile('file') ? ChatMessage::TYPE_FILE : ChatMessage::TYPE_TEXT,
            'reply_to_message_id' => $request->reply_to_message_id,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat/files', 'public');

            $messageData['attachment_path'] = $path;
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['attachment_type'] = $file->getMimeType();
            $messageData['attachment_size'] = $file->getSize();
        }

        $message = ChatMessage::create($messageData);
        $message->load(['user:id,name,email,avatar', 'replyTo.user:id,name']);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        // Create notifications for other users
        $otherUsers = $channel->activeUsers()->where('user_id', '!=', $user->id)->get();
        foreach ($otherUsers as $otherUser) {
            ChatNotification::createMessageNotification($otherUser, $message);
        }

        return response()->json([
            'message' => $message,
        ], 201);
    }

    /**
     * Edit a message
     */
    public function editMessage(Request $request, ChatMessage $message): JsonResponse
    {
        $user = auth()->user();

        if ($message->user_id !== $user->id) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('chat.error_sending_message'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $message->update([
            'message' => $request->message,
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        broadcast(new MessageEdited($message))->toOthers();

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Delete a message
     */
    public function deleteMessage(ChatMessage $message): JsonResponse
    {
        $user = auth()->user();

        if ($message->user_id !== $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $channelId = $message->channel_id;
        $messageId = $message->id;

        $message->delete();

        broadcast(new MessageDeleted($messageId, $channelId))->toOthers();

        return response()->json([
            'message' => __('chat.message_deleted'),
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request, ChatChannel $channel): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'message_ids' => 'required|array',
            'message_ids.*' => 'required|integer|exists:chat_messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $messages = ChatMessage::whereIn('id', $request->message_ids)
            ->where('channel_id', $channel->id)
            ->where('user_id', '!=', $user->id)
            ->get();

        foreach ($messages as $message) {
            $message->markAsReadBy($user);
            broadcast(new MessageRead($message, $user))->toOthers();
        }

        return response()->json([
            'message' => __('chat.message_read'),
        ]);
    }

    /**
     * Send typing indicator
     */
    public function typing(Request $request, ChatChannel $channel): JsonResponse
    {
        $user = auth()->user();

        if (!$channel->hasMember($user)) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $isTyping = $request->input('is_typing', true);

        broadcast(new UserTyping($user, $channel->id, $isTyping))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Upload file
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('chat.error_uploading_file'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->store('chat/files', 'public');

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'url' => Storage::url($path),
        ]);
    }

    /**
     * Add reaction to a message
     */
    public function reactToMessage(Request $request, ChatMessage $message): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'emoji' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if reaction already exists
        $existingReaction = $message->reactions()
            ->where('user_id', $user->id)
            ->where('emoji', $request->emoji)
            ->first();

        if ($existingReaction) {
            // Remove reaction
            $reactionId = $existingReaction->id;
            $existingReaction->delete();

            broadcast(new MessageReactionRemoved($reactionId, $message->id, $user->id, $message->channel_id))->toOthers();

            return response()->json([
                'message' => __('chat.remove_reaction'),
                'action' => 'removed',
            ]);
        }

        // Add reaction
        $reaction = ChatMessageReaction::create([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $request->emoji,
        ]);

        broadcast(new MessageReactionAdded($reaction, $message->channel_id))->toOthers();

        return response()->json([
            'reaction' => $reaction->load('user:id,name'),
            'message' => __('chat.add_reaction'),
            'action' => 'added',
        ], 201);
    }

    /**
     * Search for users to start a chat with
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $currentUser = auth()->user();
        $query = $request->input('q');

        $users = User::where('id', '!=', $currentUser->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ];
            });

        return response()->json([
            'users' => $users,
        ]);
    }
}
