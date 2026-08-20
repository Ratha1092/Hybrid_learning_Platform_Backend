<?php

namespace Tests\Feature\Auth;

use App\Domains\Auth\Models\OAuthAccount;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OAuthUnlinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_oauth_only_user_cannot_unlink_their_only_sign_in_method(): void
    {
        $user = User::factory()->oauthOnly()->create();
        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-123',
            'email' => $user->email,
            'name' => $user->name,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/auth/oauth/google')
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('oauth_accounts', ['user_id' => $user->id, 'provider' => 'google']);
    }

    public function test_oauth_only_user_can_unlink_a_provider_if_another_remains(): void
    {
        $user = User::factory()->oauthOnly()->create();
        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-123',
            'email' => $user->email,
            'name' => $user->name,
        ]);
        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => 'github',
            'provider_id' => 'github-456',
            'email' => $user->email,
            'name' => $user->name,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/auth/oauth/google')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('oauth_accounts', ['user_id' => $user->id, 'provider' => 'google']);
        $this->assertDatabaseHas('oauth_accounts', ['user_id' => $user->id, 'provider' => 'github']);
    }

    public function test_user_with_real_password_can_unlink_their_only_provider(): void
    {
        $user = User::factory()->create(['has_password' => true]);
        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-789',
            'email' => $user->email,
            'name' => $user->name,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/auth/oauth/google')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('oauth_accounts', ['user_id' => $user->id, 'provider' => 'google']);
    }
}
