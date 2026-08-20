<?php

namespace Database\Factories;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'has_password' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /** Simulates an OAuth-only sign-up: a random, unusable password hash and has_password = false. */
    public function oauthOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make(Str::random(32)),
            'has_password' => false,
        ]);
    }
}
