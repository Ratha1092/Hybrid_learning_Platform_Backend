<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Courses\Models\Course;
use Illuminate\Notifications\Notification;

class AdminCourseSubmittedNotification extends Notification
{
    public function __construct(
        public readonly Course $course,
        public readonly string $instructorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'New Course Submitted for Review',
            'message'     => "\"{$this->course->title}\" was submitted by {$this->instructorName}.",
            'type'        => 'course',
            'course_id'   => $this->course->id,
            'action_url'  => '/admin/courses',
            'action_text' => 'Review Course',
        ];
    }
}
