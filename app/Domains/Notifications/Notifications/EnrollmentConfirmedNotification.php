<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationLinks;
use App\Domains\Orders\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmedNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly string $courseTitle,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $link = NotificationLinks::frontend('/library');

        return new BroadcastMessage([
            'title'       => 'Enrollment Confirmed',
            'message'     => "You have successfully enrolled in \"{$this->courseTitle}\".",
            'type'        => NotificationType::COURSE->value,
            'link'        => $link,
            'action_text' => 'Go to My Courses',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $link = NotificationLinks::frontend('/library');

        return [
            'title'       => 'Enrollment Confirmed',
            'message'     => "You have successfully enrolled in \"{$this->courseTitle}\".",
            'type'        => NotificationType::COURSE->value,
            'order_id'    => $this->order->id,
            'link'        => $link,
            'action_text' => 'Go to My Courses',
        ];
    }
}
