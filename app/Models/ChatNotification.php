<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Notification types
     */
    public const TYPE_NEW_MESSAGE = 'new_message';
    public const TYPE_MENTION = 'mention';
    public const TYPE_USER_JOINED = 'user_joined';
    public const TYPE_USER_LEFT = 'user_left';
    public const TYPE_USER_BLOCKED = 'user_blocked';
    public const TYPE_ISSUE_ASSIGNED = 'issue_assigned';
    public const TYPE_ISSUE_STATUS_CHANGED = 'issue_status_changed';
    public const TYPE_MESSAGE_REACTION = 'message_reaction';

    /**
     * Get the user who receives the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notifiable entity (polymorphic)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Scope to filter unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to filter read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Create a new message notification
     */
    public static function createMessageNotification(User $user, ChatMessage $message): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => self::TYPE_NEW_MESSAGE,
            'notifiable_type' => ChatMessage::class,
            'notifiable_id' => $message->id,
            'data' => [
                'message' => $message->message,
                'sender_name' => $message->user->name,
                'channel_id' => $message->channel_id,
            ],
        ]);
    }

    /**
     * Create a mention notification
     */
    public static function createMentionNotification(User $user, ChatMessage $message): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => self::TYPE_MENTION,
            'notifiable_type' => ChatMessage::class,
            'notifiable_id' => $message->id,
            'data' => [
                'message' => $message->message,
                'sender_name' => $message->user->name,
                'channel_id' => $message->channel_id,
            ],
        ]);
    }

    /**
     * Create an issue assigned notification
     */
    public static function createIssueAssignedNotification(User $user, ChatIssue $issue): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => self::TYPE_ISSUE_ASSIGNED,
            'notifiable_type' => ChatIssue::class,
            'notifiable_id' => $issue->id,
            'data' => [
                'issue_title' => $issue->description,
                'priority' => $issue->priority,
                'channel_id' => $issue->channel_id,
            ],
        ]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsReadForUser(User $user): void
    {
        self::forUser($user)->unread()->update(['read_at' => now()]);
    }
}
