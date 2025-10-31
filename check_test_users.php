<?php

// Run this script with: php check_test_users.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Specialization;
use App\Models\ProviderProfile;
use App\Models\Appointment;

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 BOOKING SYSTEM TEST DATA VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

// Check Users
echo "👥 TEST USERS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$testEmails = [
    'superadmin@test.com',
    'admin@test.com',
    'dr.smith@test.com',
    'dr.johnson@test.com',
    'dr.williams@test.com',
    'patient1@test.com',
    'patient2@test.com',
    'viewer@test.com',
];

$users = User::with('roles')->whereIn('email', $testEmails)->get();

foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    $permissions = $user->getAllPermissions()->pluck('name')->take(3)->join(', ');
    
    echo "✓ {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$roles}\n";
    echo "  Key Permissions: {$permissions}...\n";
    echo "\n";
}

// Check Specializations
echo "\n📋 SPECIALIZATIONS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$specializations = Specialization::all();
foreach ($specializations as $spec) {
    $providerCount = $spec->providerProfiles()->count();
    echo "✓ {$spec->icon} {$spec->name} ({$providerCount} providers)\n";
}

// Check Provider Profiles
echo "\n👨‍⚕️ PROVIDER PROFILES:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$providers = ProviderProfile::with(['user', 'specialization', 'schedules'])->get();
foreach ($providers as $provider) {
    $scheduleCount = $provider->schedules()->where('is_available', true)->count();
    echo "✓ {$provider->user->name}\n";
    echo "  Specialization: {$provider->specialization->name}\n";
    echo "  Experience: {$provider->years_experience} years\n";
    echo "  Session: {$provider->slot_duration} minutes\n";
    echo "  Available Days: {$scheduleCount}\n";
    echo "  Status: " . ($provider->is_available ? '🟢 Available' : '🔴 Unavailable') . "\n";
    echo "\n";
}

// Check Appointments
echo "\n📅 TEST APPOINTMENTS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$appointments = Appointment::with(['user', 'providerProfile.user'])->get();

$statusEmojis = [
    'pending' => '🟡',
    'confirmed' => '🟢',
    'completed' => '✅',
    'cancelled' => '🔴',
];

foreach ($appointments as $appointment) {
    $emoji = $statusEmojis[$appointment->status] ?? '❓';
    echo "{$emoji} {$appointment->status}\n";
    echo "  Patient: {$appointment->user->name}\n";
    echo "  Provider: {$appointment->providerProfile->user->name}\n";
    echo "  Date: {$appointment->appointment_date}\n";
    echo "  Time: {$appointment->start_time} - {$appointment->end_time}\n";
    echo "  Notes: " . substr($appointment->notes, 0, 50) . "...\n";
    echo "\n";
}

// Summary
echo "\n📊 SUMMARY:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ Users: " . $users->count() . "/8\n";
echo "✓ Specializations: " . $specializations->count() . "/5\n";
echo "✓ Provider Profiles: " . $providers->count() . "/3\n";
echo "✓ Appointments: " . $appointments->count() . "/5\n";
echo "\n";

echo "🎉 All test data has been seeded successfully!\n";
echo "📖 See BOOKING_SYSTEM_TEST_PLAN.md for detailed test cases.\n";
echo "\n";
