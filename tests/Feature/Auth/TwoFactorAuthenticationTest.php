<?php

namespace Tests\Feature\Auth;

use App\Domains\Auth\Models\TwoFactorCode;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_two_factor_challenge_for_enabled_user(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'two-factor@example.com',
            'password' => Hash::make('Password1!'),
            'two_factor_enabled' => true,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password1!',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.requires_2fa', true)
            ->assertJsonStructure(['data' => ['challenge_token', 'email', 'expires_in']])
            ->assertJsonMissingPath('data.token');
    }

    public function test_two_factor_verify_requires_challenge_token_not_email_only(): void
    {
        $user = User::factory()->create([
            'email' => 'two-factor@example.com',
            'two_factor_enabled' => true,
        ]);

        TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->postJson('/api/v1/auth/2fa/verify', [
            'email' => $user->email,
            'code' => '123456',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('challenge_token');
    }

    public function test_two_factor_can_be_enabled_by_authenticated_user(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        \App\Domains\System\Models\Setting::set('enable_2fa', true, 'auth', 'boolean');
        Sanctum::actingAs($user);

        $enable = $this->postJson('/api/v1/auth/2fa/enable')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/v1/auth/2fa/verify-enable', [
            'code' => $enable->json('data.code'),
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue($user->fresh()->two_factor_enabled);
    }

    public function test_two_factor_code_is_invalidated_after_failed_attempts(): void
    {
        $user = User::factory()->create();
        $code = TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
        ]);
        $service = app(\App\Domains\Auth\Services\TwoFactorAuthService::class);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->assertFalse($service->verifyCode($user, '000000'));
        }

        $this->assertFalse($service->verifyCode($user, '123456'));
        $this->assertTrue($code->fresh()->used);
        $this->assertSame(5, $code->fresh()->attempts);
    }
}
