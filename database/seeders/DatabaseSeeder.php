<?php

namespace Database\Seeders;

use App\Domains\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(SettingsSeeder::class);

        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('admin123'),
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $superAdmin->syncRoles(['super-admin']);

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $admin->syncRoles(['admin']);

        $staffRoles = [
            'finance-manager' => 'Finance Manager',
            'accountant' => 'Accountant',
            'content-manager' => 'Content Manager',
            'moderator' => 'Moderator',
            'support-staff' => 'Support Staff',
        ];

        foreach ($staffRoles as $roleName => $label) {
            $staffUser = User::firstOrCreate([
                'email' => str_replace('-', '.', $roleName) . '@example.com',
            ], [
                'name' => $label,
                'password' => Hash::make('password'),
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]);
            $staffUser->syncRoles([$roleName]);
        }

        $testUser = User::firstOrCreate([
            'email' => 'www.rathakh1092@gmail.com',
        ], [
            'name' => 'Torn Ratha',
            'password' => Hash::make('Stunz@Ratha56'),
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $testUser->syncRoles(['student']);
        // $this->call(LargeDatasetSeeder::class);
    }
}
