<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Role-to-Role User Assignment Model
 * 
 * This handles assignments where a specific user is designated to handle
 * communications from an entire role to another role.
 * 
 * Example:
 * - from_role: "doctor"
 * - to_role: "admin"
 * - assigned_user_id: 5 (Admin User John)
 * 
 * Result: ALL doctors will see Admin User John in their chat list
 */
class ChatRoleAssignment extends Model
{
    protected $fillable = [
        'from_role',
        'to_role',
        'assigned_user_id',
        'assigned_by',
    ];

    /**
     * Get the user who is assigned to handle this role's requests
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get the admin who made this assignment
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get all users assigned to handle requests from a specific role to another role
     * 
     * @param string $fromRole The role initiating the chat (e.g., "doctor")
     * @param string $toRole The role they want to chat with (e.g., "admin")
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAssignedUsers(string $fromRole, string $toRole)
    {
        return static::where('from_role', $fromRole)
            ->where('to_role', $toRole)
            ->with('assignedUser')
            ->get()
            ->pluck('assignedUser');
    }

    /**
     * Check if there are any assigned users for this role combination
     * 
     * @param string $fromRole
     * @param string $toRole
     * @return bool
     */
    public static function hasAssignedUsers(string $fromRole, string $toRole): bool
    {
        return static::where('from_role', $fromRole)
            ->where('to_role', $toRole)
            ->exists();
    }

    /**
     * Get all role combinations that have assignments
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getAllRoleCombinations()
    {
        return static::select('from_role', 'to_role')
            ->distinct()
            ->get();
    }

    /**
     * Get all users a specific user can chat with based on their roles
     * 
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getVisibleUsersForUser(User $user)
    {
        $userRoles = $user->getRoleNames()->toArray();
        $visibleUsers = collect();

        foreach ($userRoles as $userRole) {
            // Get all role combinations where this user's role is the "from_role"
            $assignments = static::where('from_role', $userRole)
                ->with('assignedUser')
                ->get();

            foreach ($assignments as $assignment) {
                $visibleUsers->push($assignment->assignedUser);
            }
        }

        return $visibleUsers->unique('id');
    }
}
