<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\TwoFactorCode;
use App\Domains\Auth\Models\UserSession;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Jobs\Mail\SendTwoFactorEmailJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    private const LOGIN_CHALLENGE_TTL_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;

    /**
     * Generate OTP code
     */
    public function generateCode(User $user): string
    {
        $user->twoFactorCodes()->delete();

        $plainCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        TwoFactorCode::create([
            'user_id'    => $user->id,
            'code'       => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(5),
        ]);

        SendTwoFactorEmailJob::dispatch($user->id, $plainCode);

        return $plainCode;
    }

    /**
     * Verify code
     */
    public function verifyCode(User $user, string $code): bool
    {
        return DB::transaction(function () use ($user, $code) {
            $record = TwoFactorCode::where('user_id', $user->id)
                ->where('expires_at', '>', now())
                ->where('used', false)
                ->lockForUpdate()
                ->first();

            if (!$record) {
                return false;
            }

            if (Hash::check($code, $record->code)) {
                $record->update(['used' => true]);
                return true;
            }

            $attempts = $record->attempts + 1;
            $record->update([
                'attempts' => $attempts,
                'used' => $attempts >= self::MAX_ATTEMPTS,
            ]);

            return false;
        });
    }

    /**
     * Enable 2FA
     */
    public function enable(User $user): array
    {
        if (!Setting::get('enable_2fa', false)) {
            throw new \RuntimeException('Two-factor authentication is not available on this platform.');
        }

        $code = $this->generateCode($user);

        $response = [
            'message' => 'Verification code generated',
        ];

        if (in_array(app()->environment(), ['local', 'testing'], true)) {
            $response['code'] = $code;
        }

        return $response;
    }

    public function createLoginChallenge(User $user): array
    {
        $code = $this->generateCode($user);
        $challengeToken = Str::random(64);

        Cache::put(
            $this->loginChallengeCacheKey($challengeToken),
            $user->id,
            now()->addMinutes(self::LOGIN_CHALLENGE_TTL_MINUTES),
        );

        $response = [
            'requires_2fa' => true,
            'challenge_token' => $challengeToken,
            'email' => $user->email,
            'expires_in' => self::LOGIN_CHALLENGE_TTL_MINUTES * 60,
        ];

        if (in_array(app()->environment(), ['local', 'testing'], true)) {
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
        $user = $this->userFromLoginChallenge($data['challenge_token']);

        $code = $this->generateCode($user);

        $response = [
            'message' => 'Verification code generated',
            'challenge_token' => $data['challenge_token'],
            'expires_in' => self::LOGIN_CHALLENGE_TTL_MINUTES * 60,
        ];

        if (in_array(app()->environment(), ['local', 'testing'], true)) {
            $response['code'] = $code;
        }

        return $response;
    }

    /**
     * Verify during login
     */
    public function verifyLogin(array $data): array
    {
        $user = $this->userFromLoginChallenge($data['challenge_token']);

        if (!$this->verifyCode($user, $data['code'])) {
            throw new \RuntimeException('Invalid or expired code');
        }

        Cache::forget($this->loginChallengeCacheKey($data['challenge_token']));
        $user->tokens()->delete();
        $newToken = $user->createToken('api-token');
        $token = $newToken->plainTextToken;
        UserSession::record($user, $newToken->accessToken);

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

    private function userFromLoginChallenge(string $challengeToken): User
    {
        $userId = Cache::get($this->loginChallengeCacheKey($challengeToken));
        $user = $userId ? User::find($userId) : null;

        if (!$user || !$this->isEnabled($user)) {
            throw new \RuntimeException('Invalid or expired 2FA challenge');
        }

        return $user;
    }

    private function loginChallengeCacheKey(string $challengeToken): string
    {
        return "2fa_login_challenge:{$challengeToken}";
    }
}
