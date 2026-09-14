<?php

namespace Database\Seeders;

use App\Domains\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /** @var array<string, string> Passwords generated because no env override was set. */
    private array $generatedPasswords = [];

    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(SettingsSeeder::class);

        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@gmail.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make($this->resolvePassword('SEED_SUPERADMIN_PASSWORD')),
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $superAdmin->syncRoles(['super-admin']);

        // $this->call(LargeDatasetSeeder::class);

        if ($this->generatedPasswords && $this->command) {
            $this->command->warn('No env override set for these accounts — generated random passwords (save now, not shown again):');
            foreach ($this->generatedPasswords as $envKey => $password) {
                $this->command->line("  {$envKey}: {$password}");
            }
        }
    }

    /**
     * Reads a password from the given env var, or generates a strong random
     * one and records it so it can be printed once at the end of the run.
     */
    private function resolvePassword(string $envKey): string
    {
        if ($value = env($envKey)) {
            return $value;
        }

        return $this->generatedPasswords[$envKey] = Str::password(20);
    }
}
