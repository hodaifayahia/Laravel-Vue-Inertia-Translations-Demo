<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load(['user:id,name,email,avatar', 'replyTo.user:id,name']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('chat.channel.' . $this->message->channel_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'channel_id' => $this->message->channel_id,
                'user_id' => $this->message->user_id,
                'message' => $this->message->message,
                'content' => $this->message->message, // Add content field for frontend
                'type' => $this->message->type,
                'attachment_path' => $this->message->attachment_path,
                'attachment_url' => $this->message->attachment_url,
                'attachment_name' => $this->message->attachment_name,
                'attachment_type' => $this->message->attachment_type,
                'attachment_size' => $this->message->attachment_size,
                'formatted_attachment_size' => $this->message->formatted_attachment_size,
                'is_image' => $this->message->is_image,
                'is_video' => $this->message->is_video,
                'is_audio' => $this->message->is_audio,
                'is_document' => $this->message->is_document,
                'reply_to_message_id' => $this->message->reply_to_message_id,
                'is_edited' => $this->message->is_edited,
                'edited_at' => $this->message->edited_at,
                'created_at' => $this->message->created_at,
                'user' => $this->message->user,
                'reply_to' => $this->message->replyTo ? [
                    'id' => $this->message->replyTo->id,
                    'message' => $this->message->replyTo->message,
                    'content' => $this->message->replyTo->message,
                    'user' => $this->message->replyTo->user,
                ] : null,
            ],
        ];
    }
}
