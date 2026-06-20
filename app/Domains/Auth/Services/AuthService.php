<?php

namespace App\Domains\Auth\Services;

use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Auth\Resources\UserResource;

class AuthService
{
    public function register(array $data)
    {
        if (!Setting::get('registration_enabled', true)) {
            throw new \RuntimeException('Registration is currently disabled.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('student');
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

        if ($user && $this->isLockedOut($user)) {
            throw ValidationException::withMessages([
                'email' => ['Too many failed attempts. Please try again later.'],
            ]);
        }

        if (!$user || !Hash::check($data['password'], $user->password)) {
            if ($user) {
                ActivityLogService::log('failed_login', $user);
                $this->recordFailedAttempt($user);
            }

            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $this->clearFailedAttempts($user);

        if (Setting::get('email_verification_required', false) && !$user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Please verify your email address before logging in.'],
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

        $user->tokens()->delete();
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

    private function isLockedOut(User $user): bool
    {
        $maxAttempts = (int) Setting::get('max_login_attempts', 5);

        if ($maxAttempts <= 0) {
            return false;
        }

        return (int) Cache::get($this->lockoutCacheKey($user), 0) >= $maxAttempts;
    }

    private function recordFailedAttempt(User $user): void
    {
        $lockMinutes = max(1, (int) Setting::get('account_lock_duration', 15));
        $key = $this->lockoutCacheKey($user);

        Cache::put($key, (int) Cache::get($key, 0) + 1, now()->addMinutes($lockMinutes));
    }

    private function clearFailedAttempts(User $user): void
    {
        Cache::forget($this->lockoutCacheKey($user));
    }

    private function lockoutCacheKey(User $user): string
    {
        return "login_attempts:{$user->id}";
    }
}
