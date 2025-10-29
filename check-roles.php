<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Current Roles and Permissions ===\n\n";

$roles = \Spatie\Permission\Models\Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "Role: {$role->name}\n";
    echo "  Permissions: " . $role->permissions->pluck('name')->join(', ') . "\n";
    echo "  Permission Count: " . $role->permissions->count() . "\n\n";
}

echo "\n=== All Permissions ===\n\n";

$permissions = \Spatie\Permission\Models\Permission::all();
foreach ($permissions as $permission) {
    echo "- {$permission->name}\n";
}
