<?php

namespace App\Events;

use App\Models\ChatIssue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IssueCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatIssue $issue;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatIssue $issue)
    {
        $this->issue = $issue->load(['reporter:id,name', 'channel']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat.issues'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'issue.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'issue' => [
                'id' => $this->issue->id,
                'channel_id' => $this->issue->channel_id,
                'reported_by' => $this->issue->reported_by,
                'reporter_name' => $this->issue->reporter->name,
                'status' => $this->issue->status,
                'priority' => $this->issue->priority,
                'description' => $this->issue->description,
                'created_at' => $this->issue->created_at,
            ],
        ];
    }
}
