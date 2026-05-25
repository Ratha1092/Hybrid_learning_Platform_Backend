<?php

namespace App\Notifications;

use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Users\Models\InstructorVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class NewInstructorVerificationNotification extends BaseNotification
{
    use Queueable;

    public function __construct(
        public InstructorVerification $verification
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = "{$this->verification->user->name} submitted a verification request.";

        return [
            'title' => 'New Instructor Verification',
            'body' => $message,
            'message' => $message,
            'type' => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'resource_id' => $this->verification->id,
            'resource_type' => 'instructor_verification',
            'action_url' => "/admin/instructor-verifications/{$this->verification->id}",
            'format' => 'filament',
            'created_at' => now(),
        ];
    }
}
