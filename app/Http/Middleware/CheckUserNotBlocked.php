<?php

namespace App\Http\Middleware;

use App\Models\ChatChannel;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserNotBlocked
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

        // Check if trying to send a message
        if ($request->isMethod('post') && $request->route('channel')) {
            $channel = $request->route('channel');

            if ($channel instanceof ChatChannel) {
                // Check if user is blocked in this channel
                if ($channel->isUserBlocked($user)) {
                    return response()->json([
                        'message' => __('chat.you_blocked'),
                    ], 403);
                }

                // If direct message, check if the other user blocked this user
                if ($channel->isDirect()) {
                    $otherUser = $channel->getOtherUser($user);
                    if ($otherUser) {
                        $otherUserPivot = $channel->users()
                            ->where('user_id', $otherUser->id)
                            ->first();

                        if ($otherUserPivot && $otherUserPivot->pivot->is_blocked) {
                            return response()->json([
                                'message' => __('chat.blocked_you'),
                            ], 403);
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
