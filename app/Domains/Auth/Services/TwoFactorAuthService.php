<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use App\Domains\Auth\Models\TwoFactorCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TwoFactorAuthService
{
    /**
     * Generate OTP code
     */
    public function generateCode(User $user): string
    {
        $user->twoFactorCodes()->delete();

        $plainCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(5),
        ]);

        return $plainCode;
    }

    /**
     * Verify code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $record = TwoFactorCode::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->get()
            ->first(function ($item) use ($code) {
                return Hash::check($code, $item->code);
            });

        if (!$record) {
            return false;
        }

        return DB::transaction(function () use ($record) {
            $record->update(['used' => true]);

            return true;
        });
    }

    /**
     * Enable 2FA
     */
    public function enable(User $user): array
    {
        $code = $this->generateCode($user);

        $response = [
            'message' => 'Verification code generated',
        ];

        if (!app()->environment('production')) {
            $response['code'] = $code;
        }

        return $response;
    }

    public function verifyAndEnable(User $user, array $data): bool
    {
        if (!$this->verifyCode($user, $data['code'])) {
            throw new \RuntimeException('Invalid or expired code');
        }

        $user->update(['two_factor_enabled' => true]);

        ActivityLogService::log('2fa_enabled', $user);

        return true;
    }

    /**
     * Disable 2FA
     */
    public function disable(User $user, array $data = []): void
    {
        if (isset($data['password']) && !Hash::check($data['password'], $user->password)) {
            throw new \RuntimeException('Invalid password');
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);

        $user->twoFactorCodes()->delete();

        ActivityLogService::log('2fa_disabled', $user);
    }

    /**
     * Status
     */
    public function status(User $user): array
    {
        return [
            'two_factor_enabled' => $user->two_factor_enabled,
        ];
    }

    /**
     * Send code (login flow)
     */
    public function sendCode(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !$this->isEnabled($user)) {
            throw new \RuntimeException('2FA not enabled');
        }

        $code = $this->generateCode($user);

        $response = [
            'message' => 'Verification code generated',
        ];

        if (!app()->environment('production')) {
            $response['code'] = $code;
        }

        return $response;
    }

    /**
     * Verify during login
     */
    public function verifyLogin(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        if (!$this->verifyCode($user, $data['code'])) {
            throw new \RuntimeException('Invalid or expired code');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        ActivityLogService::log('login', $user);

        return [
            'token' => $token,
            'user' => new \App\Domains\Auth\Resources\UserResource($user),
        ];
    }

    /**
     * Check if enabled
     */
    public function isEnabled(User $user): bool
    {
        return (bool) $user->two_factor_enabled;
    }
}
