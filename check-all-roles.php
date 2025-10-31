<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;

echo "All roles in the system:" . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

$roles = Role::all();
foreach ($roles as $role) {
    $usersCount = $role->users()->count();
    echo "- {$role->name} ({$usersCount} users)" . PHP_EOL;
}
