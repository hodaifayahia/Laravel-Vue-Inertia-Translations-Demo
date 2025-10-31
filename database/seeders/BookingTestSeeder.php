<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Specialization;
use App\Models\ProviderProfile;
use App\Models\ProviderSchedule;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BookingTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Booking System Test Seeder...');

        // Create test specializations
        $this->command->info('📋 Creating specializations...');
        $specializations = $this->createSpecializations();

        // Create test users with different roles
        $this->command->info('👥 Creating test users...');
        $users = $this->createTestUsers();

        // Create provider profiles
        $this->command->info('👨‍⚕️ Creating provider profiles...');
        $providers = $this->createProviderProfiles($users, $specializations);

        // Create provider schedules
        $this->command->info('📅 Creating provider schedules...');
        $this->createProviderSchedules($providers);

        // Create test appointments
        $this->command->info('📝 Creating test appointments...');
        $this->createTestAppointments($users, $providers);

        $this->command->info('✅ Booking System Test Seeder completed successfully!');
        $this->command->newLine();
        $this->displayTestUsers();
    }

    /**
     * Create test specializations
     */
    private function createSpecializations(): array
    {
        $specs = [
            [
                'name' => 'Cardiology',
                'slug' => 'cardiology',
                'description' => 'Heart and cardiovascular system specialists',
                'icon' => '❤️',
            ],
            [
                'name' => 'Dermatology',
                'slug' => 'dermatology',
                'description' => 'Skin, hair, and nail care specialists',
                'icon' => '🩺',
            ],
            [
                'name' => 'Pediatrics',
                'slug' => 'pediatrics',
                'description' => 'Child health and development specialists',
                'icon' => '👶',
            ],
            [
                'name' => 'Orthopedics',
                'slug' => 'orthopedics',
                'description' => 'Bone, joint, and muscle specialists',
                'icon' => '🦴',
            ],
            [
                'name' => 'Neurology',
                'slug' => 'neurology',
                'description' => 'Brain and nervous system specialists',
                'icon' => '🧠',
            ],
        ];

        $specializations = [];
        foreach ($specs as $spec) {
            $specializations[] = Specialization::firstOrCreate(
                ['slug' => $spec['slug']],
                [
                    'name' => $spec['name'],
                    'description' => $spec['description'],
                    'icon' => $spec['icon'],
                    'is_active' => true,
                ]
            );
        }

        return $specializations;
    }

    /**
     * Create test users with different roles
     */
    private function createTestUsers(): array
    {
        $users = [];

        // Super Admin - can do everything
        $users['super_admin'] = User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $users['super_admin']->assignRole('super-admin');

        // Admin - can manage bookings
        $users['admin'] = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $users['admin']->assignRole('admin');

        // Provider 1 - Cardiologist (can provide services)
        $users['provider1'] = User::firstOrCreate(
            ['email' => 'dr.smith@test.com'],
            [
                'name' => 'Dr. John Smith',
                'password' => Hash::make('password'),
            ]
        );
        $users['provider1']->assignRole('manager');

        // Provider 2 - Dermatologist (can provide services)
        $users['provider2'] = User::firstOrCreate(
            ['email' => 'dr.johnson@test.com'],
            [
                'name' => 'Dr. Sarah Johnson',
                'password' => Hash::make('password'),
            ]
        );
        $users['provider2']->assignRole('manager');

        // Provider 3 - Pediatrician (can provide services)
        $users['provider3'] = User::firstOrCreate(
            ['email' => 'dr.williams@test.com'],
            [
                'name' => 'Dr. Emily Williams',
                'password' => Hash::make('password'),
            ]
        );
        $users['provider3']->assignRole('manager');

        // Regular Patient 1 - can only book
        $users['patient1'] = User::firstOrCreate(
            ['email' => 'patient1@test.com'],
            [
                'name' => 'Alice Patient',
                'password' => Hash::make('password'),
            ]
        );
        $users['patient1']->assignRole('user');

        // Regular Patient 2 - can only book
        $users['patient2'] = User::firstOrCreate(
            ['email' => 'patient2@test.com'],
            [
                'name' => 'Bob Patient',
                'password' => Hash::make('password'),
            ]
        );
        $users['patient2']->assignRole('user');

        // Viewer - can only view (no booking)
        $users['viewer'] = User::firstOrCreate(
            ['email' => 'viewer@test.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
            ]
        );
        $users['viewer']->assignRole('viewer');

        return $users;
    }

    /**
     * Create provider profiles
     */
    private function createProviderProfiles(array $users, array $specializations): array
    {
        $providers = [];

        // Provider 1 - Cardiologist
        $providers['provider1'] = ProviderProfile::firstOrCreate(
            ['user_id' => $users['provider1']->id],
            [
                'specialization_id' => $specializations[0]->id, // Cardiology
                'bio' => 'Experienced cardiologist with over 15 years of practice. Specialized in preventive cardiology and heart disease management. Committed to providing personalized care for each patient.',
                'years_experience' => 15,
                'slot_duration' => 45,
                'is_available' => true,
            ]
        );

        // Provider 2 - Dermatologist
        $providers['provider2'] = ProviderProfile::firstOrCreate(
            ['user_id' => $users['provider2']->id],
            [
                'specialization_id' => $specializations[1]->id, // Dermatology
                'bio' => 'Board-certified dermatologist specializing in medical and cosmetic dermatology. Expert in skin cancer detection, acne treatment, and anti-aging procedures. Passionate about helping patients achieve healthy skin.',
                'years_experience' => 10,
                'slot_duration' => 30,
                'is_available' => true,
            ]
        );

        // Provider 3 - Pediatrician
        $providers['provider3'] = ProviderProfile::firstOrCreate(
            ['user_id' => $users['provider3']->id],
            [
                'specialization_id' => $specializations[2]->id, // Pediatrics
                'bio' => 'Caring pediatrician dedicated to children\'s health and well-being. Experienced in newborn care, vaccinations, and developmental monitoring. Creating a comfortable environment for young patients and their families.',
                'years_experience' => 8,
                'slot_duration' => 30,
                'is_available' => true,
            ]
        );

        return $providers;
    }

    /**
     * Create provider schedules
     */
    private function createProviderSchedules(array $providers): void
    {
        // Provider 1 Schedule - Available Monday to Friday
        $days = [
            ['day' => 1, 'start' => '09:00', 'end' => '17:00'], // Monday
            ['day' => 2, 'start' => '09:00', 'end' => '17:00'], // Tuesday
            ['day' => 3, 'start' => '09:00', 'end' => '17:00'], // Wednesday
            ['day' => 4, 'start' => '10:00', 'end' => '18:00'], // Thursday
            ['day' => 5, 'start' => '09:00', 'end' => '15:00'], // Friday
        ];

        foreach ($days as $day) {
            ProviderSchedule::firstOrCreate(
                [
                    'provider_profile_id' => $providers['provider1']->id,
                    'day_of_week' => $day['day'],
                ],
                [
                    'start_time' => $day['start'],
                    'end_time' => $day['end'],
                    'is_available' => true,
                ]
            );
        }

        // Provider 2 Schedule - Available Tuesday to Saturday
        $days2 = [
            ['day' => 2, 'start' => '10:00', 'end' => '18:00'], // Tuesday
            ['day' => 3, 'start' => '10:00', 'end' => '18:00'], // Wednesday
            ['day' => 4, 'start' => '10:00', 'end' => '18:00'], // Thursday
            ['day' => 5, 'start' => '10:00', 'end' => '18:00'], // Friday
            ['day' => 6, 'start' => '09:00', 'end' => '14:00'], // Saturday
        ];

        foreach ($days2 as $day) {
            ProviderSchedule::firstOrCreate(
                [
                    'provider_profile_id' => $providers['provider2']->id,
                    'day_of_week' => $day['day'],
                ],
                [
                    'start_time' => $day['start'],
                    'end_time' => $day['end'],
                    'is_available' => true,
                ]
            );
        }

        // Provider 3 Schedule - Available Monday, Wednesday, Friday
        $days3 = [
            ['day' => 1, 'start' => '08:00', 'end' => '16:00'], // Monday
            ['day' => 3, 'start' => '08:00', 'end' => '16:00'], // Wednesday
            ['day' => 5, 'start' => '08:00', 'end' => '16:00'], // Friday
        ];

        foreach ($days3 as $day) {
            ProviderSchedule::firstOrCreate(
                [
                    'provider_profile_id' => $providers['provider3']->id,
                    'day_of_week' => $day['day'],
                ],
                [
                    'start_time' => $day['start'],
                    'end_time' => $day['end'],
                    'is_available' => true,
                ]
            );
        }
    }

    /**
     * Create test appointments with various statuses
     */
    private function createTestAppointments(array $users, array $providers): void
    {
        // Pending appointment
        Appointment::firstOrCreate(
            [
                'user_id' => $users['patient1']->id,
                'provider_profile_id' => $providers['provider1']->id,
                'appointment_date' => now()->addDays(3)->toDateString(),
                'start_time' => '10:00:00',
            ],
            [
                'end_time' => '10:45:00',
                'status' => 'pending',
                'notes' => 'First-time patient consultation for heart palpitations.',
            ]
        );

        // Confirmed appointment
        Appointment::firstOrCreate(
            [
                'user_id' => $users['patient1']->id,
                'provider_profile_id' => $providers['provider2']->id,
                'appointment_date' => now()->addDays(5)->toDateString(),
                'start_time' => '14:00:00',
            ],
            [
                'end_time' => '14:30:00',
                'status' => 'confirmed',
                'notes' => 'Follow-up appointment for acne treatment.',
            ]
        );

        // Completed appointment
        Appointment::firstOrCreate(
            [
                'user_id' => $users['patient2']->id,
                'provider_profile_id' => $providers['provider1']->id,
                'appointment_date' => now()->subDays(7)->toDateString(),
                'start_time' => '11:00:00',
            ],
            [
                'end_time' => '11:45:00',
                'status' => 'completed',
                'notes' => 'Annual cardiac checkup - patient is doing well.',
            ]
        );

        // Cancelled appointment
        Appointment::firstOrCreate(
            [
                'user_id' => $users['patient2']->id,
                'provider_profile_id' => $providers['provider3']->id,
                'appointment_date' => now()->subDays(2)->toDateString(),
                'start_time' => '09:00:00',
            ],
            [
                'end_time' => '09:30:00',
                'status' => 'cancelled',
                'notes' => 'Cancelled by patient due to scheduling conflict.',
            ]
        );

        // Multiple appointments for testing list/management
        Appointment::firstOrCreate(
            [
                'user_id' => $users['patient1']->id,
                'provider_profile_id' => $providers['provider3']->id,
                'appointment_date' => now()->addDays(10)->toDateString(),
                'start_time' => '15:00:00',
            ],
            [
                'end_time' => '15:30:00',
                'status' => 'pending',
                'notes' => 'Child vaccination appointment.',
            ]
        );
    }

    /**
     * Display test users for easy reference
     */
    private function displayTestUsers(): void
    {
        $this->command->info('📋 Test Users Created:');
        $this->command->newLine();
        
        $this->command->info('🔑 LOGIN CREDENTIALS (password: password for all)');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $this->command->info('👑 SUPER ADMIN (Full Access):');
        $this->command->line('   Email: superadmin@test.com');
        $this->command->line('   Can: Manage everything, view all, book, provide services');
        $this->command->newLine();
        
        $this->command->info('⚙️  ADMIN (Booking Manager):');
        $this->command->line('   Email: admin@test.com');
        $this->command->line('   Can: Manage all bookings, view all appointments');
        $this->command->newLine();
        
        $this->command->info('👨‍⚕️ PROVIDERS (Can provide services + book):');
        $this->command->line('   Email: dr.smith@test.com (Cardiologist, 15 years exp)');
        $this->command->line('   Email: dr.johnson@test.com (Dermatologist, 10 years exp)');
        $this->command->line('   Email: dr.williams@test.com (Pediatrician, 8 years exp)');
        $this->command->line('   Can: Configure profile/schedule, book appointments');
        $this->command->newLine();
        
        $this->command->info('🧑 PATIENTS (Can book only):');
        $this->command->line('   Email: patient1@test.com (Alice Patient)');
        $this->command->line('   Email: patient2@test.com (Bob Patient)');
        $this->command->line('   Can: Book appointments, view their appointments');
        $this->command->newLine();
        
        $this->command->info('👁️  VIEWER (Read-only):');
        $this->command->line('   Email: viewer@test.com');
        $this->command->line('   Can: View only (no booking access)');
        $this->command->newLine();
        
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        
        $this->command->info('📊 TEST DATA SUMMARY:');
        $this->command->line('   ✓ 5 Specializations created');
        $this->command->line('   ✓ 8 Users with different roles');
        $this->command->line('   ✓ 3 Provider profiles with schedules');
        $this->command->line('   ✓ 5 Test appointments (various statuses)');
        $this->command->newLine();
        
        $this->command->info('🧪 TEST CASES TO VERIFY:');
        $this->command->line('   1. Super Admin - Access all features');
        $this->command->line('   2. Admin - Manage bookings without provider profile');
        $this->command->line('   3. Providers - Setup profile, configure schedule, receive bookings');
        $this->command->line('   4. Patients - Browse specializations, book appointments');
        $this->command->line('   5. Viewer - Cannot access booking system');
        $this->command->line('   6. View provider details page for each doctor');
        $this->command->line('   7. Check appointment statuses (pending/confirmed/completed/cancelled)');
        $this->command->line('   8. Test availability slots per provider schedule');
    }
}
