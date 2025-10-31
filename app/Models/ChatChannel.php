<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Channel types
     */
    public const TYPE_DIRECT = 'direct';
    public const TYPE_GROUP = 'group';

    /**
     * Get the user who created the channel
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all users in the channel
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_channel_users', 'channel_id', 'user_id')
            ->withPivot(['role', 'is_blocked', 'blocked_by', 'block_reason', 'blocked_at', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * Get active users in the channel (not blocked, not left)
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('is_blocked', false)
            ->whereNull('chat_channel_users.left_at');
    }

    /**
     * Get all messages in the channel
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'channel_id');
    }

    /**
     * Get the latest message in the channel
     */
    public function latestMessage(): HasMany
    {
        return $this->messages()->latest()->limit(1);
    }

    /**
     * Get all issues related to this channel
     */
    public function issues(): HasMany
    {
        return $this->hasMany(ChatIssue::class, 'channel_id');
    }

    /**
     * Check if a user is a member of the channel
     */
    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user is blocked in the channel
     */
    public function isUserBlocked(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('is_blocked', true)
            ->exists();
    }

    /**
     * Check if a user is an admin of the channel
     */
    public function isUserAdmin(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Get unread message count for a user
     */
    public function getUnreadCountForUser(User $user): int
    {
        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();
    }

    /**
     * Check if channel is a direct message
     */
    public function isDirect(): bool
    {
        return $this->type === self::TYPE_DIRECT;
    }

    /**
     * Check if channel is a group
     */
    public function isGroup(): bool
    {
        return $this->type === self::TYPE_GROUP;
    }

    /**
     * Get the other user in a direct message channel
     */
    public function getOtherUser(User $currentUser): ?User
    {
        if (!$this->isDirect()) {
            return null;
        }

        return $this->users()
            ->where('user_id', '!=', $currentUser->id)
            ->first();
    }

    /**
     * Scope to filter direct channels
     */
    public function scopeDirect($query)
    {
        return $query->where('type', self::TYPE_DIRECT);
    }

    /**
     * Scope to filter group channels
     */
    public function scopeGroup($query)
    {
        return $query->where('type', self::TYPE_GROUP);
    }

    /**
     * Scope to filter channels for a specific user
     */
    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('is_blocked', false)
                ->whereNull('left_at');
        });
    }
}
