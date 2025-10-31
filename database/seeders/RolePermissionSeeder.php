<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            
            // Role permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission permissions
            'view permissions',
            'assign permissions',
            
            // Dashboard
            'view dashboard',
            
            // Sidebar access permissions
            'view dashboard sidebar',
            'view users sidebar',
            'view roles sidebar',
            'view permissions sidebar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - has most permissions except managing roles/permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view dashboard',
        ]);

        // Manager - can view and edit users
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view users',
            'edit users',
            'view dashboard',
            'view dashboard sidebar',
            'view users sidebar',
        ]);

        // User - basic permissions
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'view dashboard',
            'view dashboard sidebar',
        ]);
        
        // Viewer - can only view users (no edit/create/delete)
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view users',
            'view dashboard',
            'view dashboard sidebar',
            'view users sidebar',
        ]);

        // Assign super-admin role to the first user if exists
        $firstUser = \App\Models\User::first();
        if ($firstUser && !$firstUser->hasRole('super-admin')) {
            $firstUser->assignRole('super-admin');
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
