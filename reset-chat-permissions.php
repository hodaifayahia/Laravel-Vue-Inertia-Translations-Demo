<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChatPermission;

echo "🧹 Cleaning up chat permissions..." . PHP_EOL . PHP_EOL;

// Delete all existing permissions
$deletedCount = ChatPermission::count();
ChatPermission::truncate();

echo "✅ Deleted {$deletedCount} old permissions" . PHP_EOL . PHP_EOL;

echo "🔄 Creating new permission structure..." . PHP_EOL . PHP_EOL;

// Define standard roles
$roles = ['super-admin', 'admin', 'manager', 'user', 'viewer'];

$created = 0;

foreach ($roles as $fromRole) {
    foreach ($roles as $toRole) {
        // Super Admin can chat with everyone
        if ($fromRole === 'super-admin') {
            ChatPermission::create([
                'from_role' => $fromRole,
                'to_role' => $toRole,
                'can_initiate' => true,
                'can_receive' => true,
            ]);
            $created++;
            continue;
        }

        // Admin can chat with everyone
        if ($fromRole === 'admin') {
            ChatPermission::create([
                'from_role' => $fromRole,
                'to_role' => $toRole,
                'can_initiate' => true,
                'can_receive' => true,
            ]);
            $created++;
            continue;
        }

        // Manager can chat with admin, other managers, users, and viewers
        if ($fromRole === 'manager') {
            if (in_array($toRole, ['admin', 'super-admin', 'manager', 'user', 'viewer'])) {
                ChatPermission::create([
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => true,
                    'can_receive' => true,
                ]);
            } else {
                ChatPermission::create([
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => false,
                    'can_receive' => true,
                ]);
            }
            $created++;
            continue;
        }

        // User can chat with admin, manager, and other users
        if ($fromRole === 'user') {
            if (in_array($toRole, ['admin', 'manager', 'user'])) {
                ChatPermission::create([
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => true,
                    'can_receive' => true,
                ]);
            } else {
                ChatPermission::create([
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => false,
                    'can_receive' => true,
                ]);
            }
            $created++;
            continue;
        }

        // Viewer can chat with admin and manager
        if ($fromRole === 'viewer') {
            if (in_array($toRole, ['admin', 'manager'])) {
                ChatPermission::create([
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => true,
                    'can_receive' => true,
                ]);
            } else {
                ChatPermission::create([
                    'from_role' => $fromRole,
                    'to_role' => $toRole,
                    'can_initiate' => false,
                    'can_receive' => true,
                ]);
            }
            $created++;
        }
    }
}

echo "✅ Created {$created} new permissions" . PHP_EOL . PHP_EOL;

// Display summary
echo "📊 Permission Summary:" . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

$perms = ChatPermission::all();

foreach ($roles as $fromRole) {
    $canChatWith = [];
    foreach ($roles as $toRole) {
        $perm = $perms->first(function($p) use ($fromRole, $toRole) {
            return $p->from_role === $fromRole && $p->to_role === $toRole && $p->can_initiate && $p->can_receive;
        });
        
        if ($perm) {
            $canChatWith[] = $toRole;
        }
    }
    
    echo "{$fromRole}: " . implode(', ', $canChatWith) . PHP_EOL;
}

echo PHP_EOL . "✨ Chat permissions cleaned and reset successfully!" . PHP_EOL;
echo "🔗 Access settings at: http://localhost:8000/chat/permission-settings" . PHP_EOL;
