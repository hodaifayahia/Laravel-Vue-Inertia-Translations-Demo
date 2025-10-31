<?php

namespace App\Http\Controllers;

use App\Events\UserBlocked;
use App\Events\UserUnblocked;
use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Models\ChatPermission;
use App\Models\ChatUserAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class ChatAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:manage chat']);
    }

    /**
     * Display chat admin panel
     */
    public function index(): Response
    {
        return Inertia::render('Dashboard/Chat/Admin/Index');
    }

    /**
     * Get all chat permissions
     */
    public function permissions(): JsonResponse
    {
        $roles = Role::all()->pluck('name');
        $permissions = ChatPermission::all();

        $matrix = [];
        foreach ($roles as $fromRole) {
            foreach ($roles as $toRole) {
                $permission = $permissions->where('from_role', $fromRole)
                    ->where('to_role', $toRole)
                    ->first();

                $matrix[] = [
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => $permission?->can_initiate ?? false,
                    'can_receive' => $permission?->can_receive ?? false,
                ];
            }
        }

        return response()->json([
            'roles' => $roles,
            'permissions' => $matrix,
        ]);
    }

    /**
     * Update chat permissions
     */
    public function updatePermissions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_role' => 'required|string',
            'to_role' => 'required|string',
            'can_initiate' => 'required|boolean',
            'can_receive' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        ChatPermission::setPermission(
            $request->from_role,
            $request->to_role,
            $request->can_initiate,
            $request->can_receive
        );

        return response()->json([
            'message' => __('chat.permission_updated'),
        ]);
    }

    /**
     * Get user assignments
     */
    public function userAssignments(): JsonResponse
    {
        $assignments = ChatUserAssignment::with(['assignedUser', 'assignedBy'])
            ->latest()
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'assignable_role' => $assignment->assignable_role,
                    'assigned_user' => [
                        'id' => $assignment->assignedUser->id,
                        'name' => $assignment->assignedUser->name,
                        'email' => $assignment->assignedUser->email,
                    ],
                    'assigned_by' => [
                        'id' => $assignment->assignedBy->id,
                        'name' => $assignment->assignedBy->name,
                    ],
                    'created_at' => $assignment->created_at,
                ];
            });

        $roles = Role::all()->pluck('name');
        $users = User::select('id', 'name', 'email')->get();

        return response()->json([
            'assignments' => $assignments,
            'roles' => $roles,
            'users' => $users,
        ]);
    }

    /**
     * Create user assignment
     */
    public function createUserAssignment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assignable_role' => 'required|string',
            'assigned_user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if assignment already exists
        $existing = ChatUserAssignment::where('assignable_role', $request->assignable_role)
            ->where('assigned_user_id', $request->assigned_user_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => __('chat.assignment_created'),
            ], 200);
        }

        $assignment = ChatUserAssignment::create([
            'assignable_role' => $request->assignable_role,
            'assigned_user_id' => $request->assigned_user_id,
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'assignment' => $assignment->load(['assignedUser', 'assignedBy']),
            'message' => __('chat.assignment_created'),
        ], 201);
    }

    /**
     * Delete user assignment
     */
    public function deleteUserAssignment(ChatUserAssignment $assignment): JsonResponse
    {
        $assignment->delete();

        return response()->json([
            'message' => __('chat.remove_assignment'),
        ]);
    }

    /**
     * Block a user in a channel
     */
    public function blockUser(Request $request, ChatChannel $channel, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$channel->hasMember($user)) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $channel->users()->updateExistingPivot($user->id, [
            'is_blocked' => true,
            'blocked_by' => auth()->id(),
            'block_reason' => $request->reason,
            'blocked_at' => now(),
        ]);

        broadcast(new UserBlocked($user, auth()->user(), $channel, $request->reason));

        return response()->json([
            'message' => __('chat.user_blocked', ['user' => $user->name]),
        ]);
    }

    /**
     * Unblock a user in a channel
     */
    public function unblockUser(ChatChannel $channel, User $user): JsonResponse
    {
        if (!$channel->hasMember($user)) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $channel->users()->updateExistingPivot($user->id, [
            'is_blocked' => false,
            'blocked_by' => null,
            'block_reason' => null,
            'blocked_at' => null,
        ]);

        broadcast(new UserUnblocked($user, auth()->user(), $channel));

        return response()->json([
            'message' => __('chat.user_unblocked', ['user' => $user->name]),
        ]);
    }

    /**
     * Get all blocked users
     */
    public function blockedUsers(): JsonResponse
    {
        $blockedUsers = DB::table('chat_channel_users')
            ->join('users', 'chat_channel_users.user_id', '=', 'users.id')
            ->join('chat_channels', 'chat_channel_users.channel_id', '=', 'chat_channels.id')
            ->leftJoin('users as blocked_by_user', 'chat_channel_users.blocked_by', '=', 'blocked_by_user.id')
            ->where('chat_channel_users.is_blocked', true)
            ->select(
                'chat_channel_users.channel_id',
                'chat_channels.name as channel_name',
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'chat_channel_users.block_reason',
                'chat_channel_users.blocked_at',
                'blocked_by_user.id as blocked_by_id',
                'blocked_by_user.name as blocked_by_name'
            )
            ->get();

        return response()->json([
            'blocked_users' => $blockedUsers,
        ]);
    }

    /**
     * Get chat analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        $period = $request->input('period', 'week'); // day, week, month

        $startDate = match ($period) {
            'day' => now()->startOfDay(),
            'month' => now()->startOfMonth(),
            default => now()->startOfWeek(),
        };

        $totalMessages = ChatMessage::count();
        $messagesInPeriod = ChatMessage::where('created_at', '>=', $startDate)->count();
        $totalChannels = ChatChannel::count();
        $activeUsers = ChatMessage::where('created_at', '>=', $startDate)
            ->distinct('user_id')
            ->count('user_id');
        $blockedUsersCount = DB::table('chat_channel_users')
            ->where('is_blocked', true)
            ->count();

        $messagesByDay = ChatMessage::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $mostActiveChannel = ChatChannel::withCount(['messages' => function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }])
            ->orderBy('messages_count', 'desc')
            ->first();

        $topUsers = User::withCount(['chatMessages' => function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }])
            ->orderBy('chat_messages_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'message_count' => $user->chat_messages_count,
                ];
            });

        return response()->json([
            'total_messages' => $totalMessages,
            'messages_in_period' => $messagesInPeriod,
            'total_channels' => $totalChannels,
            'active_users' => $activeUsers,
            'blocked_users_count' => $blockedUsersCount,
            'messages_by_day' => $messagesByDay,
            'most_active_channel' => $mostActiveChannel ? [
                'id' => $mostActiveChannel->id,
                'name' => $mostActiveChannel->name ?? 'Direct Message',
                'message_count' => $mostActiveChannel->messages_count,
            ] : null,
            'top_users' => $topUsers,
        ]);
    }
}
