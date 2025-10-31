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

class UserBlocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $blockedUser;
    public User $blockedBy;
    public ChatChannel $channel;
    public ?string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(User $blockedUser, User $blockedBy, ChatChannel $channel, ?string $reason = null)
    {
        $this->blockedUser = $blockedUser;
        $this->blockedBy = $blockedBy;
        $this->channel = $channel;
        $this->reason = $reason;
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
            new Channel('chat.user.' . $this->blockedUser->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.blocked';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'blocked_user_id' => $this->blockedUser->id,
            'blocked_by_id' => $this->blockedBy->id,
            'blocked_by_name' => $this->blockedBy->name,
            'channel_id' => $this->channel->id,
            'reason' => $this->reason,
            'blocked_at' => now()->toISOString(),
        ];
    }
}
