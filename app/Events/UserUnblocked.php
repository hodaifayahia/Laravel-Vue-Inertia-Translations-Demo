<?php

namespace App\Events;

use App\Models\ChatChannel;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUnblocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $unblockedUser;
    public User $unblockedBy;
    public ChatChannel $channel;

    /**
     * Create a new event instance.
     */
    public function __construct(User $unblockedUser, User $unblockedBy, ChatChannel $channel)
    {
        $this->unblockedUser = $unblockedUser;
        $this->unblockedBy = $unblockedBy;
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('chat.channel.' . $this->channel->id),
            new Channel('chat.user.' . $this->unblockedUser->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.unblocked';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'unblocked_user_id' => $this->unblockedUser->id,
            'unblocked_by_id' => $this->unblockedBy->id,
            'unblocked_by_name' => $this->unblockedBy->name,
            'channel_id' => $this->channel->id,
        ];
    }
}
