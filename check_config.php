<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Current Reverb Port: " . config('broadcasting.connections.reverb.options.port') . "\n";
echo "Current Reverb Host: " . config('broadcasting.connections.reverb.options.host') . "\n";
echo "Current Reverb Key: " . config('broadcasting.connections.reverb.key') . "\n";
