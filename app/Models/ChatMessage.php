<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'channel_id',
        'user_id',
        'message',
        'type',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'reply_to_message_id',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'is_read',
        'read_by_count',
        'content',
        'attachment_url',
        'formatted_attachment_size',
        'is_image',
        'is_video',
        'is_audio',
        'is_document',
    ];

    /**
     * Message types
     */
    public const TYPE_TEXT = 'text';
    public const TYPE_FILE = 'file';
    public const TYPE_SYSTEM = 'system';

    /**
     * Get content attribute (alias for message)
     */
    public function getContentAttribute(): ?string
    {
        return $this->attributes['message'] ?? null;
    }

    /**
     * Get full URL for attachment
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }
        return asset('storage/' . $this->attachment_path);
    }

    /**
     * Check if attachment is an image
     */
    public function getIsImageAttribute(): bool
    {
        if (!$this->attachment_type) {
            return false;
        }
        return str_starts_with($this->attachment_type, 'image/');
    }

    /**
     * Check if attachment is a video
     */
    public function getIsVideoAttribute(): bool
    {
        if (!$this->attachment_type) {
            return false;
        }
        return str_starts_with($this->attachment_type, 'video/');
    }

    /**
     * Check if attachment is audio
     */
    public function getIsAudioAttribute(): bool
    {
        if (!$this->attachment_type) {
            return false;
        }
        return str_starts_with($this->attachment_type, 'audio/');
    }

    /**
     * Check if attachment is a document
     */
    public function getIsDocumentAttribute(): bool
    {
        if (!$this->attachment_type) {
            return false;
        }
        return in_array($this->attachment_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ]) || str_starts_with($this->attachment_type, 'text/');
    }

    /**
     * Get the channel this message belongs to
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(ChatChannel::class, 'channel_id');
    }

    /**
     * Get the user who sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all read receipts for this message
     */
    public function reads(): HasMany
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }

    /**
     * Get all reactions to this message
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(ChatMessageReaction::class, 'message_id');
    }

    /**
     * Get the message this is replying to
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_message_id');
    }

    /**
     * Get replies to this message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'reply_to_message_id');
    }

    /**
     * Check if message has been read by a user
     */
    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark message as read by a user
     */
    public function markAsReadBy(User $user): void
    {
        if (!$this->isReadBy($user) && $this->user_id !== $user->id) {
            $this->reads()->create([
                'user_id' => $user->id,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Check if message has attachments
     */
    public function hasAttachment(): bool
    {
        return $this->type === self::TYPE_FILE && !empty($this->attachment_path);
    }

    /**
     * Get formatted attachment size
     */
    public function getFormattedAttachmentSizeAttribute(): ?string
    {
        if (!$this->attachment_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->attachment_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get is_read attribute (for current authenticated user)
     */
    public function getIsReadAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->isReadBy(auth()->user());
    }

    /**
     * Get read_by_count attribute
     */
    public function getReadByCountAttribute(): int
    {
        return $this->reads()->count();
    }

    /**
     * Check if message is a text message
     */
    public function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    /**
     * Check if message is a file message
     */
    public function isFile(): bool
    {
        return $this->type === self::TYPE_FILE;
    }

    /**
     * Check if message is a system message
     */
    public function isSystem(): bool
    {
        return $this->type === self::TYPE_SYSTEM;
    }

    /**
     * Scope to filter text messages
     */
    public function scopeText($query)
    {
        return $query->where('type', self::TYPE_TEXT);
    }

    /**
     * Scope to filter file messages
     */
    public function scopeFile($query)
    {
        return $query->where('type', self::TYPE_FILE);
    }

    /**
     * Scope to filter system messages
     */
    public function scopeSystem($query)
    {
        return $query->where('type', self::TYPE_SYSTEM);
    }

    /**
     * Scope to filter unread messages for a user
     */
    public function scopeUnreadFor($query, User $user)
    {
        return $query->whereDoesntHave('reads', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('user_id', '!=', $user->id);
    }

    /**
     * Scope to get messages with their relationships
     */
    public function scopeWithDetails($query)
    {
        return $query->with([
            'user:id,name,email',
            'reads',
            'reactions.user:id,name',
            'replyTo.user:id,name',
        ]);
    }
}
