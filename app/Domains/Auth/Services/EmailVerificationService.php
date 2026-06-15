<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\EmailVerificationToken;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Resend\Laravel\Facades\Resend;

class EmailVerificationService
{
    /**
     * Generate verification token
     */
    public function generateToken(User $user): array
    {
        // Delete old tokens
        $user->emailVerificationTokens()->delete();

        $plainToken = Str::random(64);

        $record = EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => Hash::make($plainToken), // 🔐 hashed
            'expires_at' => now()->addHours(24),
        ]);

        $link = $this->getVerificationLink($plainToken);

        Resend::emails()->send([
            'from'    => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'      => [$user->email],
            'subject' => 'Verify Your Email Address — ' . config('app.name'),
            'html'    => view('emails.auth.verify-email', ['user' => $user, 'verificationLink' => $link])->render(),
        ]);

        return [
            'record' => $record,
            'token'  => $plainToken,
            'link'   => $link,
        ];
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token): User
    {
        $verification = EmailVerificationToken::where('expires_at', '>', now())
            ->where('used', false)
            ->get()
            ->first(function ($record) use ($token) {
                return Hash::check($token, $record->token);
            });

        if (!$verification) {
            throw new \RuntimeException('Invalid or expired verification token');
        }

        return DB::transaction(function () use ($verification) {
            $verification->update(['used' => true]);

            $verification->user->update([
                'email_verified_at' => now()
            ]);

            return $verification->user->refresh();
        });
    }

    public function isVerified(User $user): bool
    {
        return !is_null($user->email_verified_at);
    }

    public function getVerificationLink(string $token): string
    {
        $frontendUrl = config('app.frontend_url');

        return "{$frontendUrl}/verify-email/{$token}";
    }
}
