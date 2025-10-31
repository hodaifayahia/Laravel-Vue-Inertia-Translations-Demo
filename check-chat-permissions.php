<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChatPermission;

$perms = ChatPermission::all();

echo "Total chat permissions: " . $perms->count() . PHP_EOL . PHP_EOL;

echo "Role-to-Role Chat Permissions:" . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

foreach ($perms as $perm) {
    $initiate = $perm->can_initiate ? '✓ initiate' : '✗ initiate';
    $receive = $perm->can_receive ? '✓ receive' : '✗ receive';
    echo "{$perm->from_role} → {$perm->to_role}: {$initiate}, {$receive}" . PHP_EOL;
}

echo PHP_EOL . "Testing user-user permission:" . PHP_EOL;

// Get two test users
$user1 = \App\Models\User::first();
$user2 = \App\Models\User::skip(1)->first();

if ($user1 && $user2) {
    echo "User 1: {$user1->name} (roles: " . $user1->getRoleNames()->implode(', ') . ")" . PHP_EOL;
    echo "User 2: {$user2->name} (roles: " . $user2->getRoleNames()->implode(', ') . ")" . PHP_EOL;
    echo "User 1 can chat with User 2: " . ($user1->canChatWith($user2) ? 'YES' : 'NO') . PHP_EOL;
    echo "User 2 can chat with User 1: " . ($user2->canChatWith($user1) ? 'YES' : 'NO') . PHP_EOL;
}
