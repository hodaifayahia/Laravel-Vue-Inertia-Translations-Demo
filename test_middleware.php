<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Middleware Resolution ===\n\n";

// Test if middleware classes can be resolved
$middlewareToTest = [
    'check.chat.permission' => \App\Http\Middleware\CheckChatPermission::class,
    'check.user.not.blocked' => \App\Http\Middleware\CheckUserNotBlocked::class,
];

foreach ($middlewareToTest as $alias => $class) {
    echo "Testing alias: '$alias' => $class\n";
    
    try {
        // Check if class exists
        if (class_exists($class)) {
            echo "  ✓ Class exists\n";
            
            // Try to instantiate
            $instance = new $class();
            echo "  ✓ Can instantiate\n";
            
            // Check if it has handle method
            if (method_exists($instance, 'handle')) {
                echo "  ✓ Has handle() method\n";
            } else {
                echo "  ✗ Missing handle() method\n";
            }
        } else {
            echo "  ✗ Class does not exist!\n";
        }
    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Test actual middleware registration
echo "=== Testing Middleware Aliases ===\n\n";
try {
    $router = app('router');
    echo "Router available: ✓\n";
} catch (\Exception $e) {
    echo "Router error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
