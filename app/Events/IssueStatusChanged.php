<?php

namespace App\Events;

use App\Models\ChatIssue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IssueStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatIssue $issue;
    public string $oldStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatIssue $issue, string $oldStatus)
    {
        $this->issue = $issue->load(['assignee:id,name', 'reporter:id,name']);
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('chat.issues'),
        ];

        if ($this->issue->assigned_to) {
            $channels[] = new Channel('chat.user.' . $this->issue->assigned_to);
        }

        if ($this->issue->reported_by) {
            $channels[] = new Channel('chat.user.' . $this->issue->reported_by);
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'issue.status.changed';
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
                'old_status' => $this->oldStatus,
                'new_status' => $this->issue->status,
                'assigned_to' => $this->issue->assigned_to,
                'assignee_name' => $this->issue->assignee?->name,
                'resolution_notes' => $this->issue->resolution_notes,
                'resolved_at' => $this->issue->resolved_at,
            ],
        ];
    }
}
