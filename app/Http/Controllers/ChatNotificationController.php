<?php

namespace App\Http\Controllers;

use App\Models\ChatNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatNotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = $user->chatNotifications()->with('notifiable')->latest();

        // Filter by read/unread
        if ($request->has('unread') && $request->unread) {
            $query->unread();
        }

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        $notifications = $query->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->unreadChatNotifications()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(ChatNotification $notification): JsonResponse
    {
        $user = auth()->user();

        if ($notification->user_id !== $user->id) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'notification' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();

        ChatNotification::markAllAsReadForUser($user);

        return response()->json([
            'message' => __('chat.mark_all_read'),
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(ChatNotification $notification): JsonResponse
    {
        $user = auth()->user();

        if ($notification->user_id !== $user->id) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'message' => __('Notification deleted'),
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function destroyAllRead(): JsonResponse
    {
        $user = auth()->user();

        $user->chatNotifications()->read()->delete();

        return response()->json([
            'message' => __('All read notifications deleted'),
        ]);
    }
}
