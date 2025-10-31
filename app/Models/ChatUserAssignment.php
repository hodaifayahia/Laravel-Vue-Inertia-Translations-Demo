<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatUserAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignable_role',
        'assigned_user_id',
        'assigned_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who was assigned
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get the user who created the assignment (Super Admin)
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if a user is assigned to a specific role
     */
    public static function isUserAssigned(User $user, string $role): bool
    {
        return self::where('assigned_user_id', $user->id)
            ->where('assignable_role', $role)
            ->exists();
    }

    /**
     * Get all users assigned to a specific role
     */
    public static function getUsersForRole(string $role)
    {
        return self::where('assignable_role', $role)
            ->with('assignedUser')
            ->get()
            ->pluck('assignedUser');
    }

    /**
     * Get all role assignments for a user
     */
    public static function getAssignmentsForUser(User $user)
    {
        return self::where('assigned_user_id', $user->id)->get();
    }

    /**
     * Check if two users can communicate based on assignments
     */
    public static function canCommunicate(User $user1, User $user2): bool
    {
        $user1Roles = $user1->getRoleNames();
        $user2Roles = $user2->getRoleNames();

        // Check if user1 has an assignment to any of user2's roles
        foreach ($user1Roles as $role1) {
            foreach ($user2Roles as $role2) {
                if (self::isUserAssigned($user2, $role1) || self::isUserAssigned($user1, $role2)) {
                    return true;
                }
            }
        }

        return false;
    }
}
