<?php

namespace Database\Seeders;

use App\Models\ChatPermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ChatPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create chat permissions
        $permissions = [
            'view chat',
            'send messages',
            'manage chat',
            'block users',
            'view all conversations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Create default chat permissions for role-to-role communication
        $this->createRoleToChatPermissions();
    }

    /**
     * Assign chat permissions to existing roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - all permissions
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                'view chat',
                'send messages',
                'manage chat',
                'block users',
                'view all conversations',
            ]);
        }

        // Admin - management permissions
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'view chat',
                'send messages',
                'manage chat',
                'block users',
            ]);
        }

        // Manager - chat with anyone
        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $manager->givePermissionTo([
                'view chat',
                'send messages',
            ]);
        }

        // User - basic chat permissions
        $user = Role::where('name', 'user')->first();
        if ($user) {
            $user->givePermissionTo([
                'view chat',
                'send messages',
            ]);
        }

        // Viewer - basic chat permissions
        $viewer = Role::where('name', 'viewer')->first();
        if ($viewer) {
            $viewer->givePermissionTo([
                'view chat',
                'send messages',
            ]);
        }
    }

    /**
     * Create default role-to-role chat permissions
     */
    private function createRoleToChatPermissions(): void
    {
        // Define all roles (lowercase to match RolePermissionSeeder)
        $roles = ['super-admin', 'admin', 'manager', 'user', 'viewer'];

        foreach ($roles as $fromRole) {
            foreach ($roles as $toRole) {
                // Super Admin can chat with everyone
                if ($fromRole === 'super-admin') {
                    ChatPermission::updateOrCreate(
                        ['from_role' => $fromRole, 'to_role' => $toRole],
                        ['can_initiate' => true, 'can_receive' => true]
                    );
                    continue;
                }

                // Admin can chat with everyone
                if ($fromRole === 'admin') {
                    ChatPermission::updateOrCreate(
                        ['from_role' => $fromRole, 'to_role' => $toRole],
                        ['can_initiate' => true, 'can_receive' => true]
                    );
                    continue;
                }

                // Manager can chat with admin, other managers, and users
                if ($fromRole === 'manager') {
                    if (in_array($toRole, ['admin', 'manager', 'user', 'viewer'])) {
                        ChatPermission::updateOrCreate(
                            ['from_role' => $fromRole, 'to_role' => $toRole],
                            ['can_initiate' => true, 'can_receive' => true]
                        );
                    } else {
                        ChatPermission::updateOrCreate(
                            ['from_role' => $fromRole, 'to_role' => $toRole],
                            ['can_initiate' => false, 'can_receive' => true]
                        );
                    }
                    continue;
                }

                // User can chat with admin and manager
                if ($fromRole === 'user') {
                    if (in_array($toRole, ['admin', 'manager', 'user'])) {
                        ChatPermission::updateOrCreate(
                            ['from_role' => $fromRole, 'to_role' => $toRole],
                            ['can_initiate' => true, 'can_receive' => true]
                        );
                    } else {
                        ChatPermission::updateOrCreate(
                            ['from_role' => $fromRole, 'to_role' => $toRole],
                            ['can_initiate' => false, 'can_receive' => true]
                        );
                    }
                    continue;
                }

                // Viewer can chat with admin and manager
                if ($fromRole === 'viewer') {
                    if (in_array($toRole, ['admin', 'manager'])) {
                        ChatPermission::updateOrCreate(
                            ['from_role' => $fromRole, 'to_role' => $toRole],
                            ['can_initiate' => true, 'can_receive' => true]
                        );
                    } else {
                        ChatPermission::updateOrCreate(
                            ['from_role' => $fromRole, 'to_role' => $toRole],
                            ['can_initiate' => false, 'can_receive' => true]
                        );
                    }
                }
            }
        }

        $this->command->info('Chat permissions created successfully!');
    }
}
