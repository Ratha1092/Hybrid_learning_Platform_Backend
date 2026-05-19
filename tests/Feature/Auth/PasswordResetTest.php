<?php

namespace Tests\Feature\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_reset_with_valid_api_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('OldPassword1!'),
        ]);

        $forgot = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $forgot->assertOk();

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $forgot->json('data.token'),
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_password_reset_rejects_invalid_token(): void
    {
        $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'invalid-token',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])
            ->assertBadRequest()
            ->assertJsonPath('success', false);
    }
}
