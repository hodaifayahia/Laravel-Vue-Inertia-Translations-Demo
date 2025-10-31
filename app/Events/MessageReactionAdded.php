<?php

namespace App\Events;

use App\Models\ChatMessageReaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessageReaction $reaction;
    public int $channelId;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessageReaction $reaction, int $channelId)
    {
        $this->reaction = $reaction->load('user:id,name');
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
        return 'message.reaction.added';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'reaction' => [
                'id' => $this->reaction->id,
                'message_id' => $this->reaction->message_id,
                'user_id' => $this->reaction->user_id,
                'user_name' => $this->reaction->user->name,
                'emoji' => $this->reaction->emoji,
                'created_at' => $this->reaction->created_at,
            ],
        ];
    }
}
