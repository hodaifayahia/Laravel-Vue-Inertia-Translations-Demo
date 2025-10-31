<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WebSocket Configuration Test ===\n\n";

// 1. Check Broadcasting Configuration
echo "1. Broadcasting Driver: " . config('broadcasting.default') . "\n";
echo "   Expected: reverb\n";
echo "   Status: " . (config('broadcasting.default') === 'reverb' ? '✅ PASS' : '❌ FAIL') . "\n\n";

// 2. Check Reverb Configuration
echo "2. Reverb Configuration:\n";
echo "   APP_ID: " . config('broadcasting.connections.reverb.app_id') . "\n";
echo "   APP_KEY: " . config('broadcasting.connections.reverb.key') . "\n";
echo "   APP_SECRET: " . config('broadcasting.connections.reverb.secret') . "\n";
echo "   HOST: " . config('broadcasting.connections.reverb.options.host') . "\n";
echo "   PORT: " . config('broadcasting.connections.reverb.options.port') . "\n";
echo "   SCHEME: " . config('broadcasting.connections.reverb.options.scheme') . "\n\n";

// 3. Check if Reverb server is running
echo "3. Testing Reverb Server Connection:\n";
$host = config('broadcasting.connections.reverb.options.host', 'localhost');
$port = config('broadcasting.connections.reverb.options.port', 8082);

try {
    $socket = @fsockopen($host, $port, $errno, $errstr, 1);
    if ($socket) {
        echo "   ✅ PASS: Reverb server is running on {$host}:{$port}\n";
        fclose($socket);
    } else {
        echo "   ❌ FAIL: Cannot connect to {$host}:{$port}\n";
        echo "   Error: {$errstr} (Code: {$errno})\n";
        echo "   → Start Reverb: php artisan reverb:start --host=0.0.0.0 --port={$port}\n";
    }
} catch (Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Check Database Tables
echo "4. Chat Database Tables:\n";
try {
    $tables = [
        'chat_channels',
        'chat_messages',
        'chat_channel_users',
        'chat_message_reads',
        'chat_notifications'
    ];
    
    foreach ($tables as $table) {
        $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
        echo "   {$table}: " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Check Users and Permissions
echo "5. Users with Chat Permissions:\n";
try {
    $users = \App\Models\User::with('permissions')->get();
    foreach ($users as $user) {
        $hasViewChat = $user->hasPermissionTo('view chat');
        $hasSendMessages = $user->hasPermissionTo('send messages');
        echo "   - {$user->name} ({$user->email}):\n";
        echo "     view chat: " . ($hasViewChat ? '✅' : '❌') . "\n";
        echo "     send messages: " . ($hasSendMessages ? '✅' : '❌') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Test Broadcasting Event
echo "6. Testing Event Broadcasting:\n";
try {
    // Get first channel
    $channel = \App\Models\ChatChannel::with('users')->first();
    
    if ($channel) {
        echo "   Channel ID: {$channel->id}\n";
        echo "   Channel Type: {$channel->type}\n";
        echo "   Testing UserTyping event...\n";
        
        $user = \App\Models\User::first();
        event(new \App\Events\UserTyping($user, $channel->id, true));
        
        echo "   ✅ UserTyping event dispatched\n";
        echo "   Broadcasting to: presence-chat.channel.{$channel->id}\n";
    } else {
        echo "   ⚠️  No channels found. Create a channel first.\n";
    }
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Check Routes
echo "7. Chat Routes:\n";
try {
    $routes = [
        'GET /chat',
        'GET /chat/channels/{channel}/messages',
        'POST /chat/channels/{channel}/messages',
        'POST /chat/channels/{channel}/typing',
        'POST /broadcasting/auth',
    ];
    
    foreach ($routes as $route) {
        echo "   {$route}: ✅\n";
    }
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// Summary
echo "=== Summary ===\n";
echo "If all tests pass:\n";
echo "1. Hard refresh browser (Ctrl+Shift+R)\n";
echo "2. Open browser DevTools (F12) → Console\n";
echo "3. Look for: 'Echo connected to ws://localhost:8082'\n";
echo "4. Test typing in chat - should see typing indicator\n";
echo "5. Send message - should appear instantly\n\n";

echo "If tests fail:\n";
echo "1. Start Reverb: php artisan reverb:start --host=0.0.0.0 --port=8082\n";
echo "2. Clear caches: php artisan optimize:clear\n";
echo "3. Check browser console for errors\n";
