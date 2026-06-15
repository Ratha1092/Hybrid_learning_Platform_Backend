<?php

namespace App\Domains\Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InstructorApprovedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Admin Approved',
            'message' => 'Your instructor form has been approved.Please log out and log back in to your instructor dashboard to get started.',
            'type' => 'instructor_approved',
            'link' => env('FRONTEND_URL', 'http://localhost:3000') . '/instructor/login',
            'action_text' => 'Go to Instructor Dashboard',
        ];
    }
}
