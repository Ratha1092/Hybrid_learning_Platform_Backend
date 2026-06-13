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
            'title' => 'Application Approved',
            'message' => 'Your instructor application has been approved. Please logout and login again to access your instructor dashboard.',
            'type' => 'instructor_approved',
            'link' => env('FRONTEND_URL', 'http://localhost:3000') . '/instructor/login',
            'action_text' => 'Go to Instructor Dashboard',
        ];
    }
}
