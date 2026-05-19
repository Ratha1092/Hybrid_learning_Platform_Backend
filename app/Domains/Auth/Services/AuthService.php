<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Auth\Resources\UserResource;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
        ]);

        ActivityLogService::log('registration', $user);

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => new UserResource($user),
        ];
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            if ($user) {
                ActivityLogService::log('failed_login', $user);
            }

            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        if ($user->two_factor_enabled) {
            $code = app(TwoFactorAuthService::class)->generateCode($user);

            $response = [
                'requires_2fa' => true,
                'email' => $user->email,
            ];

            if (!app()->environment('production')) {
                $response['code'] = $code;
            }

            return $response;
        }

        // Remove old tokens
        $user->tokens()->delete();

        // Create fresh token
        $token = $user->createToken('api-token')->plainTextToken;

        ActivityLogService::log('login', $user);

        return [
            'token' => $token,
            'user' => new UserResource($user),
        ];
    }

    public function logout($user)
    {
        $user?->currentAccessToken()?->delete();
    }
}