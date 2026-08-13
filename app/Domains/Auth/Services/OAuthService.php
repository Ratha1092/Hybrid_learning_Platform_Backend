<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\OAuthAccount;
use App\Domains\Auth\Models\UserSession;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Domains\Auth\Resources\UserResource;
use App\Domains\Auth\Services\ActivityLogService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;

class OAuthService
{
    public function handleGoogle(array $data)
    {
        if (!Setting::get('enable_google_login', true)) {
            throw new RuntimeException('Google sign-in is currently disabled.');
        }

        try {
            return DB::transaction(function () use ($data) {
                $provider = 'google';
                $providerId = $data['provider_id'];

                $oauthAccount = OAuthAccount::where('provider', $provider)
                    ->where('provider_id', $providerId)
                    ->first();

                if ($oauthAccount) {
                    $user = $oauthAccount->user;
                    // Backfill avatar onto the user row if it was never saved there.
                    if (!$user->avatar && $oauthAccount->avatar) {
                        $user->update(['avatar' => $oauthAccount->avatar]);
                    }
                    return $this->loginUser($user);
                }

                $user = User::withTrashed()->where('email', $data['email'])->first();

                if ($user && $user->trashed()) {
                    throw new RuntimeException('This email is associated with a deleted account. Please contact support.');
                }

                if (!$user) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make(Str::random(32)),
                        'email_verified_at' => now(),
                        'avatar' => $data['avatar'] ?? null,
                    ]);

                    $user->assignRole('student');
                } elseif (!$user->avatar && !empty($data['avatar'])) {
                    // Existing email account linking Google for the first time.
                    $user->update(['avatar' => $data['avatar']]);
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
        } catch (QueryException $e) {
            return $this->recoverFromUniqueViolation($e, $data['email']);
        }
    }

    public function handleGithub(string $code): array
    {
        try {
            $tokenResponse = Socialite::driver('github')
                ->stateless()
                ->getAccessTokenResponse($code);
        } catch (GuzzleException $e) {
            Log::warning('GitHub OAuth token exchange failed', ['error' => $e->getMessage()]);
            throw new RuntimeException('GitHub sign-in failed. Please try again.');
        }

        if (empty($tokenResponse['access_token'])) {
            throw new RuntimeException('Failed to obtain GitHub access token');
        }

        try {
            $socialUser = Socialite::driver('github')
                ->stateless()
                ->userFromToken($tokenResponse['access_token']);
        } catch (GuzzleException $e) {
            Log::warning('GitHub OAuth user fetch failed', ['error' => $e->getMessage()]);
            throw new RuntimeException('GitHub sign-in failed. Please try again.');
        }

        $email = $socialUser->getEmail();
        if (!$email) {
            throw new RuntimeException('GitHub account has no accessible email address. Please make your email public on GitHub and try again.');
        }

        try {
            return DB::transaction(function () use ($socialUser, $email) {
                $provider   = 'github';
                $providerId = (string) $socialUser->getId();

                $oauthAccount = OAuthAccount::where('provider', $provider)
                    ->where('provider_id', $providerId)
                    ->first();

                if ($oauthAccount) {
                    return $this->loginUser($oauthAccount->user);
                }

                $user = User::withTrashed()->where('email', $email)->first();

                if ($user && $user->trashed()) {
                    throw new RuntimeException('This email is associated with a deleted account. Please contact support.');
                }

                if (!$user) {
                    $user = User::create([
                        'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'GitHub User',
                        'email'             => $email,
                        'password'          => Hash::make(Str::random(32)),
                        'email_verified_at' => now(),
                    ]);

                    $user->assignRole('student');
                }

                OAuthAccount::create([
                    'user_id'     => $user->id,
                    'provider'    => $provider,
                    'provider_id' => $providerId,
                    'email'       => $email,
                    'name'        => $socialUser->getName() ?? $socialUser->getNickname(),
                    'avatar'      => $socialUser->getAvatar() ?? null,
                    'data'        => $socialUser->user,
                ]);

                return $this->loginUser($user, true);
            });
        } catch (QueryException $e) {
            return $this->recoverFromUniqueViolation($e, $email);
        }
    }

    private function recoverFromUniqueViolation(QueryException $e, string $email)
    {
        $sqlState = $e->errorInfo[0] ?? null;
        $isUniqueViolation = $sqlState === '23505'
            || str_contains(strtolower($e->getMessage()), 'unique constraint')
            || str_contains(strtolower($e->getMessage()), 'duplicate key');

        if ($isUniqueViolation) {
            $user = User::withTrashed()->where('email', $email)->first();
            if ($user && $user->trashed()) {
                throw new RuntimeException('This email is associated with a deleted account. Please contact support.');
            }
            if ($user) {
                return $this->loginUser($user);
            }
        }

        throw $e;
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
        if ($user->status === User::STATUS_SUSPENDED) {
            throw new RuntimeException(User::SUSPENDED_MESSAGE);
        }

        $newToken = $user->createToken('api-token');
        $token = $newToken->plainTextToken;
        UserSession::record($user, $newToken->accessToken);
        if (\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::fromFrontend(request())) {
            Auth::guard('web')->login($user, true);
        }

        ActivityLogService::log('login', $user);

        return [
            'token' => $token,
            'user' => new UserResource($user),
            'is_new_user' => $isNew,
        ];
    }
}
