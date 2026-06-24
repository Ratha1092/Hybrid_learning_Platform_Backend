<?php

namespace App\Jobs\Mail;

use App\Domains\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Resend\Laravel\Facades\Resend;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly int    $userId,
        public readonly string $verificationLink,
    ) {}

    public function tags(): array
    {
        return ['mail', 'verification', "user:{$this->userId}"];
    }

    public function middleware(): array
    {
        return [new RateLimited('resend-emails')];
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            return;
        }

        Resend::emails()->send([
            'from'    => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'      => [$user->email],
            'subject' => 'Verify Your Email Address — ' . config('app.name'),
            'html'    => view('emails.auth.verify-email', [
                'user'             => $user,
                'verificationLink' => $this->verificationLink,
            ])->render(),
        ]);
    }
}
