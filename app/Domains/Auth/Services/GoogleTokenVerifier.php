<?php

namespace App\Domains\Auth\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GoogleTokenVerifier
{
    private const CERTS_URL = 'https://www.googleapis.com/oauth2/v3/certs';
    private const CERTS_CACHE_KEY = 'google_oauth_certs';
    private const VALID_ISSUERS = ['accounts.google.com', 'https://accounts.google.com'];

    /**
     * Verify a Google-issued id_token and return the trusted claims.
     * Throws RuntimeException if the token is missing, expired, forged, or
     * not verified for this app.
     */
    public function verify(string $idToken, ?string $expectedNonce = null): array
    {
        $clientId = config('services.google.client_id');

        if (empty($clientId)) {
            throw new RuntimeException('Google sign-in is not configured.');
        }

        $payload = $this->decode($idToken, $this->fetchCerts());

        if (!in_array($payload['iss'] ?? null, self::VALID_ISSUERS, true)) {
            throw new RuntimeException('Invalid Google sign-in token.');
        }

        if (($payload['aud'] ?? null) !== $clientId) {
            throw new RuntimeException('Invalid Google sign-in token.');
        }

        if ($expectedNonce !== null && ($payload['nonce'] ?? null) !== $expectedNonce) {
            throw new RuntimeException('Invalid Google sign-in session. Please try again.');
        }

        if (empty($payload['sub'])) {
            throw new RuntimeException('Invalid Google sign-in token.');
        }

        if (empty($payload['email']) || empty($payload['email_verified'])) {
            throw new RuntimeException('Your Google account email is not verified.');
        }

        return [
            'provider_id' => (string) $payload['sub'],
            'email' => strtolower((string) $payload['email']),
            'name' => (string) ($payload['name'] ?? explode('@', $payload['email'])[0]),
            'avatar' => $payload['picture'] ?? null,
        ];
    }

    private function decode(string $idToken, array $certs): array
    {
        JWT::$leeway = 60;

        try {
            return (array) JWT::decode($idToken, JWK::parseKeySet($certs));
        } catch (Throwable $e) {
            // Certs may have rotated since we cached them; refresh once and retry.
            Cache::forget(self::CERTS_CACHE_KEY);

            try {
                $freshCerts = $this->fetchCerts();

                return (array) JWT::decode($idToken, JWK::parseKeySet($freshCerts));
            } catch (Throwable $retryException) {
                Log::warning('Google id_token verification failed', ['error' => $retryException->getMessage()]);
                throw new RuntimeException('Invalid Google sign-in token.');
            }
        }
    }

    private function fetchCerts(): array
    {
        return Cache::remember(self::CERTS_CACHE_KEY, now()->addHours(6), function () {
            $response = Http::timeout(10)->get(self::CERTS_URL);

            if (!$response->successful()) {
                throw new RuntimeException('Unable to verify Google sign-in at this time. Please try again.');
            }

            return $response->json();
        });
    }
}
