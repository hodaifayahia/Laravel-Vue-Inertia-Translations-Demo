<?php

namespace App\Http\Middleware;

use App\Models\ChatChannel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckChatPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super Admin bypass
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Check if user has permission to use chat
        if (!$user->can('view chat')) {
            return response()->json([
                'message' => __('chat.no_permission_to_chat'),
            ], 403);
        }

        // If accessing a specific channel, check channel permissions
        if ($request->route('channel')) {
            $channel = $request->route('channel');

            if ($channel instanceof ChatChannel) {
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
            }
        }

        return $next($request);
    }
}
