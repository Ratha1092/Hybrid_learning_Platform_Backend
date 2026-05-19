<?php

namespace Tests\Feature;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_can_be_retrieved_through_api(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/users/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Test User');
    }

    public function test_profile_information_can_be_updated_through_api(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->putJson('/api/v1/users/profile', [
            'name' => 'Updated User',
            'bio' => 'Short bio',
            'phone' => '123456789',
            'country' => 'Cambodia',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Updated User');

        $this->assertSame('Updated User', $user->fresh()->name);
    }
}
