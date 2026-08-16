<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InstructorApprovedNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Application Approved',
            'message'     => 'Your instructor application has been approved. Log in to start creating courses.',
            'type'        => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/instructor/dashboard',
            'action_text' => 'Go to Instructor Dashboard',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Admin Approved',
            'message' => 'Your instructor form has been approved.Please log out and log back in to your instructor dashboard to get started.',
            'type' => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'link' => env('FRONTEND_URL', 'http://localhost:3000') . '/instructor/dashboard',
            'action_text' => 'Go to Instructor Dashboard',
        ];
    }
}
