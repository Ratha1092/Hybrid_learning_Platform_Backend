<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use App\Domains\Auth\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Request password reset without leaking whether an email exists.
     */
    public function requestReset(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return [];
        }

        $token = $this->generateToken($user)['token'];

        if (app()->environment('production')) {
            return [];
        }

        return [
            'reset_link' => $this->getResetLink($token),
            'token' => $token,
        ];
    }

    /**
     * Generate password reset token
     */
    public function generateToken(User $user): array
    {
        $user->passwordResetTokens()->delete();

        $plainToken = Str::random(64);

        PasswordResetToken::create([
            'user_id' => $user->id,
            'token' => Hash::make($plainToken), // 🔐 hashed
            'expires_at' => now()->addHours(1),
        ]);

        return [
            'token' => $plainToken,
        ];
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $reset = PasswordResetToken::where('expires_at', '>', now())
            ->where('used', false)
            ->get()
            ->first(function ($record) use ($token) {
                return Hash::check($token, $record->token);
            });

        if (!$reset) {
            throw new \RuntimeException('Invalid or expired reset token');
        }

        return DB::transaction(function () use ($reset, $newPassword) {
            $reset->update(['used' => true]);

            $user = $reset->user;

            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            // revoke tokens
            $user->tokens()->delete();

            return true;
        });
    }

    /**
     * Get reset link
     */
    public function getResetLink(string $token): string
    {
        $frontendUrl = config('app.frontend_url');

        return "{$frontendUrl}/reset-password/{$token}";
    }

    /**
     * Verify token exists and is valid
     */
    public function isTokenValid(string $token): bool
    {
        return PasswordResetToken::where('expires_at', '>', now())
            ->where('used', false)
            ->get()
            ->contains(function ($record) use ($token) {
                return Hash::check($token, $record->token);
            });
    }

    public function verifyToken(string $token): bool
    {
        if (!$this->isTokenValid($token)) {
            throw new \RuntimeException('Invalid or expired reset token');
        }

        return true;
    }
}
