<?php

namespace App\Policies;

use App\Domains\Courses\Models\Lesson;
use App\Domains\Users\Models\User;

class LessonPolicy
{
    public function view(?User $user, Lesson $lesson): bool
    {
        if ($lesson->is_preview) {
            return true;
        }
        if (! $user) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }
        $course = $lesson->section?->course;

        if (
            $course &&
            (int) $course->instructor_id === (int) $user->id
        ) {
            return true;
        }
        if (! $course) {
            return false;
        }
        return $user->hasEnrolledCourse(
            $course->id
        );
    }
}