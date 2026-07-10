<?php

namespace App\Domains\Courses\Observers;

use App\Domains\Courses\Models\Section;

class SectionObserver
{
    public function deleted(Section $section): void
    {
        $section->lessons()->each(fn ($lesson) => $lesson->delete());
    }

    public function restored(Section $section): void
    {
        $section->lessons()->onlyTrashed()->each(fn ($lesson) => $lesson->restore());
    }
}
