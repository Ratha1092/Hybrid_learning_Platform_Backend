<?php

namespace App\Jobs\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Resend\Laravel\Facades\Resend;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly string $email,
        public readonly string $code,
    ) {}

    public function tags(): array
    {
        return ['mail', 'otp', "email:{$this->email}"];
    }

    public function middleware(): array
    {
        return [new RateLimited('resend-emails')];
    }

    public function handle(): void
    {
        Resend::emails()->send([
            'from'    => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'      => [$this->email],
            'subject' => 'Your Verification Code — ' . config('app.name'),
            'html'    => view('emails.auth.otp', [
                'email' => $this->email,
                'code'  => $this->code,
            ])->render(),
        ]);
    }
}
