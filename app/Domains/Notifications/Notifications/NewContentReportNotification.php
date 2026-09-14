<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class NewContentReportNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly int $reportId,
        public readonly string $reporterName,
        public readonly string $reportableType,
    ) {}

    public function via(object $notifiable): array
    {
        return NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $url = "/admin/content-reports/{$this->reportId}";

        return new BroadcastMessage([
            'title'       => 'New Content Report',
            'message'     => "{$this->reporterName} reported a {$this->reportableType}.",
            'type'        => NotificationType::SYSTEM->value,
            'link'        => $url,
            'action_text' => 'Review Report',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $url = "/admin/content-reports/{$this->reportId}";

        return [
            'title'       => 'New Content Report',
            'message'     => "{$this->reporterName} reported a {$this->reportableType}.",
            'type'        => NotificationType::SYSTEM->value,
            'link'        => $url,
            'action_text' => 'Review Report',
        ];
    }
}
