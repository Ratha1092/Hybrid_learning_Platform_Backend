<?php

namespace Tests\Feature;

use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set(
            key: 'maintenance_mode',
            value: true,
            group: 'general',
            type: 'boolean',
            description: 'When enabled, blocks non-staff access to the platform.',
            isPublic: true,
        );
    }

    public function test_public_platform_api_is_blocked_during_maintenance(): void
    {
        $this->getJson('/api/v1/courses')
            ->assertStatus(503)
            ->assertJsonPath('success', false);
    }

    public function test_non_staff_authenticated_api_is_blocked_during_maintenance(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/users/me')
            ->assertStatus(503)
            ->assertJsonPath('success', false);
    }

    public function test_staff_authenticated_api_can_continue_during_maintenance(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('admin');

        Sanctum::actingAs($staff);

        $this->getJson('/api/v1/users/me')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_login_remains_available_during_maintenance(): void
    {
        $user = User::factory()->create([
            'email' => 'staff-login@example.com',
            'password' => Hash::make('Password1!'),
        ]);
        $user->assignRole('admin');

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password1!',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_registration_is_blocked_during_maintenance(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'New Student',
            'email' => 'new-student@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])
            ->assertStatus(503)
            ->assertJsonPath('success', false);
    }
}
