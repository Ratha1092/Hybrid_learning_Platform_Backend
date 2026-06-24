<?php

namespace App\Jobs\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Resend\Laravel\Facades\Resend;

class SendSecurityAlertEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly string $attackerIp,
        public readonly int    $attemptCount,
        public readonly array  $recentAttempts,
        public readonly string $adminEmail,
        public readonly string $adminName,
    ) {}

    public function tags(): array
    {
        return ['mail', 'security-alert', "ip:{$this->attackerIp}"];
    }

    public function handle(): void
    {
        Resend::emails()->send([
            'from'    => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'      => [$this->adminEmail],
            'subject' => "Security Alert: {$this->attemptCount} failed login attempts from {$this->attackerIp}",
            'html'    => view('emails.security.alert', [
                'attackerIp'     => $this->attackerIp,
                'attemptCount'   => $this->attemptCount,
                'recentAttempts' => $this->recentAttempts,
            ])->render(),
        ]);
    }
}
