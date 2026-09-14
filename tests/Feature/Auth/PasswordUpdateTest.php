<?php

namespace Tests\Feature\Auth;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_existing_password_must_supply_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword1!'),
            'has_password' => true,
        ]);

        Sanctum::actingAs($user);

        $this->putJson('/api/v1/auth/password', [
            'current_password' => 'WrongPassword1!',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])
            ->assertBadRequest()
            ->assertJsonPath('success', false);

        $this->assertTrue(Hash::check('OldPassword1!', $user->fresh()->password));
    }

    public function test_user_with_existing_password_can_change_it_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword1!'),
            'has_password' => true,
        ]);

        Sanctum::actingAs($user);

        $this->putJson('/api/v1/auth/password', [
            'current_password' => 'OldPassword1!',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $fresh = $user->fresh();
        $this->assertTrue(Hash::check('NewPassword1!', $fresh->password));
        $this->assertTrue($fresh->has_password);
    }

    public function test_oauth_only_user_can_set_a_first_password_without_current_password(): void
    {
        $user = User::factory()->oauthOnly()->create();
        $this->assertFalse($user->has_password);

        Sanctum::actingAs($user);

        $this->putJson('/api/v1/auth/password', [
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $fresh = $user->fresh();
        $this->assertTrue($fresh->has_password);
        $this->assertTrue(Hash::check('NewPassword1!', $fresh->password));
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->oauthOnly()->create();

        Sanctum::actingAs($user);

        $this->putJson('/api/v1/auth/password', [
            'password' => 'NewPassword1!',
            'password_confirmation' => 'Mismatch1!',
        ])
            ->assertUnprocessable();

        $this->assertFalse($user->fresh()->has_password);
    }
}
