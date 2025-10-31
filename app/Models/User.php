<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'provider',
        'provider_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all chat channels the user is a member of
     */
    public function chatChannels(): BelongsToMany
    {
        return $this->belongsToMany(ChatChannel::class, 'chat_channel_users', 'user_id', 'channel_id')
            ->withPivot(['role', 'is_blocked', 'blocked_by', 'block_reason', 'blocked_at', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * Get active chat channels (not blocked, not left)
     */
    public function activeChatChannels(): BelongsToMany
    {
        return $this->chatChannels()
            ->wherePivot('is_blocked', false)
            ->whereNull('chat_channel_users.left_at');
    }

    /**
     * Get all messages sent by the user
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get all chat notifications for the user
     */
    public function chatNotifications(): HasMany
    {
        return $this->hasMany(ChatNotification::class);
    }

    /**
     * Get unread chat notifications
     */
    public function unreadChatNotifications(): HasMany
    {
        return $this->chatNotifications()->whereNull('read_at');
    }

    /**
     * Get chat issues reported by the user
     */
    public function reportedChatIssues(): HasMany
    {
        return $this->hasMany(ChatIssue::class, 'reported_by');
    }

    /**
     * Get chat issues assigned to the user
     */
    public function assignedChatIssues(): HasMany
    {
        return $this->hasMany(ChatIssue::class, 'assigned_to');
    }

    /**
     * Get chat user assignments
     */
    public function chatAssignments(): HasMany
    {
        return $this->hasMany(ChatUserAssignment::class, 'assigned_user_id');
    }

    /**
     * Check if user can chat with another user based on roles and permissions
     */
    public function canChatWith(User $otherUser): bool
    {
        // Super Admin can chat with anyone
        if ($this->hasRole(['super-admin', 'Super Admin'])) {
            return true;
        }

        $userRoles = $this->getRoleNames();
        $otherUserRoles = $otherUser->getRoleNames();

        // Check role-based permissions
        foreach ($userRoles as $userRole) {
            foreach ($otherUserRoles as $otherRole) {
                if (ChatPermission::canCommunicate($userRole, $otherRole)) {
                    return true;
                }
            }
        }

        // Check user-specific assignments
        if (ChatUserAssignment::canCommunicate($this, $otherUser)) {
            return true;
        }

        return false;
    }

    /**
     * Get total unread messages count
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return ChatMessage::whereHas('channel.users', function ($query) {
            $query->where('user_id', $this->id);
        })
        ->where('user_id', '!=', $this->id)
        ->whereDoesntHave('reads', function ($query) {
            $query->where('user_id', $this->id);
        })
        ->count();
    }

    /**
     * Check if user is online (implement your logic here)
     */
    public function isOnline(): bool
    {
        // This can be implemented using cache or a dedicated online_status field
        // For now, return false as placeholder
        return false;
    }

    /**
     * Get the provider profile for this user.
     */
    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    /**
     * Get appointments where user is the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Check if user is a provider (has book-sys permission).
     */
    public function isProvider(): bool
    {
        return $this->hasPermissionTo('book-sys');
    }

    /**
     * Check if user can book appointments (has can-book permission).
     */
    public function canBook(): bool
    {
        return $this->hasPermissionTo('can-book');
    }
}
