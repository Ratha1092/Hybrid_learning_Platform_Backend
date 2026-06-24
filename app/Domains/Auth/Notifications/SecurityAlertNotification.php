<?php

namespace App\Domains\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityAlertNotification extends Notification
{
    public function __construct(
        public readonly string $attackerIp,
        public readonly int    $attemptCount,
        public readonly array  $recentAttempts,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $securityUrl = url('/admin/security-log?ip=' . urlencode($this->attackerIp));

        return (new MailMessage())
            ->subject('[Security Alert] ' . $this->attemptCount . ' failed login attempts from ' . $this->attackerIp)
            ->view('emails.security.alert', [
                'ip'             => $this->attackerIp,
                'count'          => $this->attemptCount,
                'recentAttempts' => $this->recentAttempts,
                'securityUrl'    => $securityUrl,
                'recipientName'  => $notifiable->name ?? 'Admin',
            ]);
    }
}
