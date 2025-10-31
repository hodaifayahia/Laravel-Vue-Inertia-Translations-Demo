<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BookingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create booking permissions
        $permissions = [
            'book-sys' => 'Can provide booking services (provider)',
            'can-book' => 'Can book appointments',
            'manage bookings' => 'Can manage all bookings',
            'view bookings' => 'Can view bookings',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['guard_name' => 'web']
            );
        }

        // Assign permissions to roles
        $superAdmin = Role::where('name', 'super-admin')->first();
        $admin = Role::where('name', 'admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $user = Role::where('name', 'user')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo(['book-sys', 'can-book', 'manage bookings', 'view bookings']);
        }

        if ($admin) {
            $admin->givePermissionTo(['book-sys', 'can-book', 'manage bookings', 'view bookings']);
        }

        if ($manager) {
            $manager->givePermissionTo(['book-sys', 'can-book', 'view bookings']);
        }

        if ($user) {
            $user->givePermissionTo(['can-book']);
        }

        $this->command->info('Booking permissions created and assigned successfully!');
    }
}
