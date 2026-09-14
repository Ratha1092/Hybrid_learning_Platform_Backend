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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;

class OAuthService
{
    public function __construct(
        private GoogleTokenVerifier $googleTokenVerifier
    ) {}

    public function handleGoogle(array $data)
    {
        if (!Setting::get('enable_google_login', true)) {
            throw new RuntimeException('Google sign-in is currently disabled.');
        }

        $data = $this->googleTokenVerifier->verify($data['id_token'], $data['nonce']);

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
                        'has_password' => false,
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

    public function handleGithub(string $code, string $codeVerifier): array
    {
        try {
            $tokenResponse = Http::asForm()
                ->acceptJson()
                ->timeout(10)
                ->post('https://github.com/login/oauth/access_token', [
                    'client_id' => config('services.github.client_id'),
                    'client_secret' => config('services.github.client_secret'),
                    'code' => $code,
                    'redirect_uri' => config('services.github.redirect'),
                    'code_verifier' => $codeVerifier,
                ]);
        } catch (\Throwable $e) {
            Log::warning('GitHub OAuth token exchange failed', ['error' => $e->getMessage()]);
            throw new RuntimeException('GitHub sign-in failed. Please try again.');
        }

        if (!$tokenResponse->successful() || empty($tokenResponse->json('access_token'))) {
            throw new RuntimeException('Failed to obtain GitHub access token');
        }

        $accessToken = $tokenResponse->json('access_token');

        try {
            $socialUser = Socialite::driver('github')
                ->stateless()
                ->userFromToken($accessToken);
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
                        'has_password'      => false,
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
        $identity = $this->verifyLinkCredential($data['provider'], $data['credential']);

        $existing = OAuthAccount::where('provider', $identity['provider'])
            ->where('provider_id', $identity['provider_id'])
            ->first();
        if ($existing && $existing->user_id !== $user->id) {
            throw new \RuntimeException('This OAuth account is already linked to another user.');
        }
        if ($user->oauthAccounts()->where('provider', $identity['provider'])->exists()) {
            throw new \RuntimeException('OAuth account already linked');
        }

        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $identity['provider'],
            'provider_id' => $identity['provider_id'],
            'email' => $identity['email'],
            'name' => $identity['name'],
            'avatar' => $identity['avatar'],
        ]);
    }

    private function verifyLinkCredential(string $provider, string $credential): array
    {
        if ($provider === 'google') {
            $identity = $this->googleTokenVerifier->verify($credential);
            return ['provider' => 'google', ...$identity];
        }

        try {
            $socialUser = Socialite::driver('github')->stateless()->userFromToken($credential);
        } catch (GuzzleException $e) {
            Log::warning('GitHub OAuth link verification failed', ['error' => $e->getMessage()]);
            throw new RuntimeException('GitHub account verification failed.');
        }

        $email = $socialUser->getEmail();
        if (!$email) {
            throw new RuntimeException('GitHub account has no accessible email address.');
        }

        return [
            'provider' => 'github',
            'provider_id' => (string) $socialUser->getId(),
            'email' => strtolower($email),
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'GitHub User',
            'avatar' => $socialUser->getAvatar(),
        ];
    }

    public function unlink($user, string $provider)
    {
        // A random placeholder password is set for every OAuth sign-up (it's
        // never null), so this can't check "does password exist" — it has to
        // check has_password, and only block when this is the user's last
        // remaining way to log in (no real password AND no other provider left).
        if (!$user->has_password && $user->oauthAccounts()->count() <= 1) {
            throw new \RuntimeException('Set a password before unlinking your only sign-in method.');
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
