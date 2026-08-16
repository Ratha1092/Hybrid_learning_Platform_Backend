<?php

namespace App\Domains\Courses\Observers;

use App\Domains\Courses\Models\Lesson;

class LessonObserver
{
    public function deleted(Lesson $lesson): void
    {
        $lesson->attachments()->each(fn ($attachment) => $attachment->delete());
    }

    public function restored(Lesson $lesson): void
    {
        $lesson->attachments()->onlyTrashed()->each(fn ($attachment) => $attachment->restore());
    }
}
