<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'reported_by',
        'assigned_to',
        'status',
        'priority',
        'description',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Issue statuses
     */
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';

    /**
     * Issue priorities
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    /**
     * Get the channel this issue is related to
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(ChatChannel::class, 'channel_id');
    }

    /**
     * Get the user who reported the issue
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user assigned to resolve the issue
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if issue is open
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if issue is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if issue is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    /**
     * Mark issue as in progress
     */
    public function markAsInProgress(): void
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    /**
     * Resolve the issue
     */
    public function resolve(string $resolutionNotes = null): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution_notes' => $resolutionNotes,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Assign issue to a user
     */
    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Scope to filter open issues
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope to filter in-progress issues
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope to filter resolved issues
     */
    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    /**
     * Scope to filter by priority
     */
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by assignee
     */
    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }

    /**
     * Scope to filter by reporter
     */
    public function scopeReportedBy($query, User $user)
    {
        return $query->where('reported_by', $user->id);
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => __('chat.priority_low'),
            self::PRIORITY_MEDIUM => __('chat.priority_medium'),
            self::PRIORITY_HIGH => __('chat.priority_high'),
            default => $this->priority,
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => __('chat.status_open'),
            self::STATUS_IN_PROGRESS => __('chat.status_in_progress'),
            self::STATUS_RESOLVED => __('chat.status_resolved'),
            default => $this->status,
        };
    }
}
