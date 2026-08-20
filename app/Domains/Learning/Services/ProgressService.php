<?php

namespace App\Domains\Learning\Services;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Notifications\Notifications\CourseCompletionNotification;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public function getProgress(
        User $user,
        Lesson $lesson,
    ): LessonProgress {

        // Match on the actual unique key (user_id, lesson_id) only — the row's
        // course_id can be null/stale on old rows, and including it here made
        // firstOrCreate() try to INSERT a duplicate, throwing a unique-constraint
        // QueryException that the global handler surfaces as a 400.
        return LessonProgress::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'course_id' => $lesson->section->course_id,
            ]
        );
    }

    public function updateLessonProgress(
        User $user,
        Lesson $lesson,
        array $data
    ): LessonProgress {

        return DB::transaction(function () use (
            $user,
            $lesson,
            $data
        ) {

            $course = $lesson->section->course;

            $watchTime = $data['watch_time'] ?? 0;

            $duration = $data['duration']
                ?? $lesson->duration
                ?? 0;
            $percentage = $this->calculatePercentage(
                $watchTime,
                $duration
            );

            $isCompleted = (
                $data['is_completed']
                ?? false
            ) || $percentage >= 90;

            $progress = LessonProgress::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'course_id' => $course->id,
                    'watch_time' => $watchTime,
                    'duration' => $duration,
                    'last_position' =>
                        $data['last_position'] ?? 0,
                    'progress_percentage' => $percentage,
                    'is_completed' => $isCompleted,
                    'completed_at' => $isCompleted
                        ? now()
                        : null,

                    'last_watched_at' => now(),
                ]
            );

            $this->syncEnrollmentProgress(
                $user,
                $course
            );

            return $progress->refresh();
        });
    }

    public function syncEnrollmentProgress(
        User $user,
        Course $course
    ): void {

        $totalLessons = $course->lessons()->count();

        if ($totalLessons === 0) {
            return;
        }

        $completedLessons = LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        $percentage = round(
            ($completedLessons / $totalLessons) * 100,
            2
        );

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return;
        }

        $enrollment->update([
            'progress_percentage' => $percentage,
            'last_accessed_at' => now(),
        ]);

        if ($percentage >= 100) {
            $wasAlreadyCompleted = $enrollment->status === 'completed';

            $enrollment->markCompleted();

            if (!$wasAlreadyCompleted && Setting::get('course_completion_notification', true)) {
                $user->notify(new CourseCompletionNotification($course->id, $course->title));
            }
        }
    }

    private function calculatePercentage(
        int|float $watchTime,
        int|float $duration
    ): float {

        if ($duration <= 0) {
            return 0;
        }

        return min(
            100,
            round(($watchTime / $duration) * 100, 2)
        );
    }
}