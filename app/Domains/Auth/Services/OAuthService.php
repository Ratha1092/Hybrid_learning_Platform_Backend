<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\OAuthAccount;
use App\Domains\Users\Models\User;
use App\Domains\Auth\Resources\UserResource;
use App\Domains\Auth\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OAuthService
{
    public function handleGoogle(array $data)
    {
        return DB::transaction(function () use ($data) {
            $provider = 'google';
            $providerId = $data['provider_id'];

            $oauthAccount = OAuthAccount::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($oauthAccount) {
                return $this->loginUser($oauthAccount->user);
            }

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                    'role' => 'student',
                ]);
            }

            OAuthAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerId,
                'email' => $data['email'],
                'name' => $data['name'],
                'avatar' => $data['avatar'] ?? null,
                'data' => $data,
            ]);

            return $this->loginUser($user, true);
        });
    }

    public function link($user, array $data)
    {
        if ($user->oauthAccounts()->where('provider', $data['provider'])->exists()) {
            throw new \RuntimeException('OAuth account already linked');
        }

        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $data['provider'],
            'provider_id' => $data['provider_id'],
            'email' => $data['email'],
            'name' => $data['name'],
            'avatar' => $data['avatar'] ?? null,
        ]);
    }

    public function unlink($user, string $provider)
    {
        if (!$user->password) {
            throw new \RuntimeException('Set password before unlinking');
        }

        $user->oauthAccounts()->where('provider', $provider)->delete();
    }

    public function getLinkedAccounts($user)
    {
        return $user->oauthAccounts()
            ->select('id', 'provider', 'email', 'avatar', 'created_at')
            ->get();
    }

    private function loginUser($user, bool $isNew = false)
    {
        $token = $user->createToken('api-token')->plainTextToken;

        ActivityLogService::log('login', $user);

        return [
            'token' => $token,
            'user' => new UserResource($user),
            'is_new_user' => $isNew,
        ];
    }
}
