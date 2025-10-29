<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Users and Their Roles ===\n\n";

$users = User::with('roles')->get();

if ($users->isEmpty()) {
    echo "No users found in the database.\n";
} else {
    foreach ($users as $user) {
        echo "User: {$user->name} ({$user->email})\n";
        if ($user->roles->isEmpty()) {
            echo "  Roles: None\n";
        } else {
            echo "  Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
        }
        echo "\n";
    }
}

echo "\n=== Available Roles ===\n\n";
$roles = \Spatie\Permission\Models\Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "- {$role->name}\n";
    if ($role->permissions->isNotEmpty()) {
        echo "  Permissions: " . $role->permissions->pluck('name')->join(', ') . "\n";
    }
}

echo "\n";
