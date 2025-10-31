<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "Creating/updating permissions...\n";

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
    echo "  - $permission\n";
}

echo "\nCreating/updating roles...\n";

// Super Admin - has all permissions
$superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
$superAdmin->syncPermissions(Permission::all());
echo "  - super-admin (all permissions)\n";

// Admin - has most permissions except managing roles/permissions
$admin = Role::firstOrCreate(['name' => 'admin']);
$admin->syncPermissions([
    'view users',
    'create users',
    'edit users',
    'delete users',
    'view dashboard',
    'view dashboard sidebar',
    'view users sidebar',
]);
echo "  - admin\n";

// Manager - can view and edit users
$manager = Role::firstOrCreate(['name' => 'manager']);
$manager->syncPermissions([
    'view users',
    'edit users',
    'view dashboard',
    'view dashboard sidebar',
    'view users sidebar',
]);
echo "  - manager\n";

// User - basic permissions
$user = Role::firstOrCreate(['name' => 'user']);
$user->syncPermissions([
    'view dashboard',
    'view dashboard sidebar',
]);
echo "  - user\n";

// Viewer - can only view users (no edit/create/delete)
$viewer = Role::firstOrCreate(['name' => 'viewer']);
$viewer->syncPermissions([
    'view users',
    'view dashboard',
    'view dashboard sidebar',
    'view users sidebar',
]);
echo "  - viewer\n";

echo "\nDone! Roles and permissions seeded successfully!\n";
