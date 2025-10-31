<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $reactionId;
    public int $messageId;
    public int $userId;
    public int $channelId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $reactionId, int $messageId, int $userId, int $channelId)
    {
        $this->reactionId = $reactionId;
        $this->messageId = $messageId;
        $this->userId = $userId;
        $this->channelId = $channelId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('chat.channel.' . $this->channelId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.reaction.removed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'reaction_id' => $this->reactionId,
            'message_id' => $this->messageId,
            'user_id' => $this->userId,
        ];
    }
}
