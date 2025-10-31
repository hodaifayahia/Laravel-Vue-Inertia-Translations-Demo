<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_role',
        'to_role',
        'can_initiate',
        'can_receive',
    ];

    protected $casts = [
        'can_initiate' => 'boolean',
        'can_receive' => 'boolean',
    ];

    /**
     * Check if a role can initiate a conversation with another role
     */
    public static function canInitiate(string $fromRole, string $toRole): bool
    {
        $permission = self::where('from_role', $fromRole)
            ->where('to_role', $toRole)
            ->first();

        return $permission ? $permission->can_initiate : false;
    }

    /**
     * Check if a role can receive messages from another role
     */
    public static function canReceive(string $fromRole, string $toRole): bool
    {
        $permission = self::where('from_role', $fromRole)
            ->where('to_role', $toRole)
            ->first();

        return $permission ? $permission->can_receive : false;
    }

    /**
     * Check if two roles can communicate
     */
    public static function canCommunicate(string $role1, string $role2): bool
    {
        return self::canInitiate($role1, $role2) && self::canReceive($role2, $role1);
    }

    /**
     * Get all permissions for a role
     */
    public static function getPermissionsForRole(string $role): array
    {
        return self::where('from_role', $role)
            ->orWhere('to_role', $role)
            ->get()
            ->toArray();
    }

    /**
     * Update or create a permission
     */
    public static function setPermission(
        string $fromRole,
        string $toRole,
        bool $canInitiate,
        bool $canReceive
    ): self {
        return self::updateOrCreate(
            [
                'from_role' => $fromRole,
                'to_role' => $toRole,
            ],
            [
                'can_initiate' => $canInitiate,
                'can_receive' => $canReceive,
            ]
        );
    }
}
