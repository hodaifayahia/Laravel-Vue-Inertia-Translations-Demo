<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Chat Setup ===\n\n";

// Test 1: Check users
echo "1. Checking Users:\n";
$userCount = \App\Models\User::count();
echo "   Total users: $userCount\n";

if ($userCount > 0) {
    $user = \App\Models\User::first();
    echo "   First user: {$user->name} ({$user->email})\n";
    
    // Check if user has roles
    if (method_exists($user, 'roles')) {
        $roles = $user->roles->pluck('name')->join(', ');
        echo "   Roles: " . ($roles ?: 'None') . "\n";
    }
} else {
    echo "   ⚠️  No users found! Run: php artisan db:seed\n";
}
echo "\n";

// Test 2: Check chat channels
echo "2. Checking Chat Channels:\n";
$channelCount = \App\Models\ChatChannel::count();
echo "   Total channels: $channelCount\n";

if ($channelCount > 0) {
    $channel = \App\Models\ChatChannel::with('users')->first();
    echo "   First channel: {$channel->name} (Type: {$channel->type})\n";
    echo "   Members: {$channel->users->count()}\n";
} else {
    echo "   ℹ️  No channels yet (will be created when users start chatting)\n";
}
echo "\n";

// Test 3: Check chat messages
echo "3. Checking Chat Messages:\n";
$messageCount = \App\Models\ChatMessage::count();
echo "   Total messages: $messageCount\n";
if ($messageCount > 0) {
    echo "   Messages exist in database\n";
} else {
    echo "   ℹ️  No messages yet (normal for new setup)\n";
}
echo "\n";

// Test 4: Check broadcasting configuration
echo "4. Checking Broadcasting Config:\n";
echo "   Broadcast driver: " . config('broadcasting.default') . "\n";
echo "   Reverb App ID: " . config('broadcasting.connections.reverb.app_id') . "\n";
echo "   Reverb App Key: " . config('broadcasting.connections.reverb.key') . "\n";
echo "   Reverb Host: " . config('broadcasting.connections.reverb.host') . "\n";
echo "   Reverb Port: " . config('broadcasting.connections.reverb.port') . "\n";
echo "\n";

// Test 5: Check if routes are registered
echo "5. Checking Routes:\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$chatRoutes = collect($routes)->filter(function ($route) {
    return str_starts_with($route->uri(), 'chat');
})->count();
echo "   Chat routes registered: $chatRoutes\n";

$broadcastingAuth = collect($routes)->filter(function ($route) {
    return $route->uri() === 'broadcasting/auth';
})->count();
echo "   Broadcasting auth route: " . ($broadcastingAuth > 0 ? '✓ Registered' : '✗ Missing') . "\n";
echo "\n";

// Test 6: Check permissions
echo "6. Checking Chat Permissions:\n";
$chatPermissions = \Spatie\Permission\Models\Permission::where('name', 'like', '%chat%')->get();
echo "   Chat permissions: {$chatPermissions->count()}\n";
foreach ($chatPermissions as $permission) {
    echo "   - {$permission->name}\n";
}
echo "\n";

echo "=== Setup Test Complete ===\n";
echo "\nNext Steps:\n";
echo "1. Make sure you have at least one user (run 'php artisan db:seed' if needed)\n";
echo "2. Start Reverb: php artisan reverb:start\n";
echo "3. Start Vite: npm run dev\n";
echo "4. Visit: http://127.0.0.1:8000/dashboard/chat\n";
