<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Granting Chat Permissions to All Users ===\n\n";

$users = \App\Models\User::all();
$permissions = ['view chat', 'send messages'];

foreach ($users as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    
    foreach ($permissions as $permission) {
        if (!$user->hasPermissionTo($permission)) {
            $user->givePermissionTo($permission);
            echo "  ✓ Granted: {$permission}\n";
        } else {
            echo "  - Already has: {$permission}\n";
        }
    }
    echo "\n";
}

echo "=== All users now have chat permissions! ===\n";
