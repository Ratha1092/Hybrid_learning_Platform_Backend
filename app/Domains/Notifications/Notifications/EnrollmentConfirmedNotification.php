<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
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
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Enrollment Confirmed',
            'message'     => "You have successfully enrolled in \"{$this->courseTitle}\".",
            'type'        => 'enrollment_confirmed',
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/my-courses',
            'action_text' => 'Go to My Courses',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Enrollment Confirmed',
            'message'     => "You have successfully enrolled in \"{$this->courseTitle}\".",
            'type'        => 'enrollment_confirmed',
            'order_id'    => $this->order->id,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/my-courses',
            'action_text' => 'Go to My Courses',
        ];
    }
}
