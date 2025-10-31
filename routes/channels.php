<?php

use App\Models\ChatChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat presence channel for online status
Broadcast::channel('chat.presence', function ($user) {
    if ($user->can('view chat')) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
        ];
    }
});

// Chat channel (conversation)
Broadcast::channel('chat.channel.{channelId}', function ($user, $channelId) {
    $channel = ChatChannel::find($channelId);
    
    if (!$channel) {
        return false;
    }

    // Check if user is a member and not blocked
    if ($channel->hasMember($user) && !$channel->isUserBlocked($user)) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
        ];
    }

    return false;
});

// User-specific channel for private notifications
Broadcast::channel('chat.user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Chat issues channel (admins only)
Broadcast::channel('chat.issues', function ($user) {
    return $user->hasRole(['Super Admin', 'Admin']);
});
