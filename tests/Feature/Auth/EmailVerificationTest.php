<?php

namespace Tests\Feature\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_can_be_verified_through_api(): void
    {
        $user = User::factory()->unverified()->create();

        Sanctum::actingAs($user);

        $send = $this->postJson('/api/v1/auth/email/send');

        $send->assertOk();

        $this->postJson('/api/v1/auth/email/verify', [
            'token' => $send->json('data.token'),
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_email_is_not_verified_with_invalid_token(): void
    {
        $user = User::factory()->unverified()->create();

        $this->postJson('/api/v1/auth/email/verify', [
            'token' => 'invalid-token',
        ])
            ->assertBadRequest()
            ->assertJsonPath('success', false);

        $this->assertNull($user->fresh()->email_verified_at);
    }
}
