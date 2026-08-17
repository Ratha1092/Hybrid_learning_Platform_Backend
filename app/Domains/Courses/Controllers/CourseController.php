<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Analytics\Models\CourseView;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\System\Models\Setting;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $query = Course::with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->withCount([
                'reviews as reviews_count' => fn ($q) => $q->where('is_approved', true),
                'sections',
                'enrollments as students_count',
            ])
            ->withSum('lessons as total_duration_seconds', 'duration')
            ->where('is_published', true);

        if ($instructorId = request('instructor_id')) {
            $query->where('instructor_id', (int) $instructorId);
        }

        if ($search = request('search')) {
            $query->where('title', 'ilike', "%{$search}%");
        }

        $courses = $query->latest()->get();

        $courses->each(function ($course) {
            $course->average_rating = $course->average_rating ? round($course->average_rating, 1) : null;
        });

    return ApiResponse::success($courses, 'Courses retrieved successfully');
}

    public function show($slug)
    {
        $course = Cache::remember("courses.slug.{$slug}", 3600, fn() =>
            Course::with([
                'instructor:id,name,avatar',
                'category:id,name,slug',
                'sections' => function ($q) {
                    $q->orderBy('order')->with([
                        'lessons' => function ($q) {
                            $q->orderBy('order');
                        }
                    ]);
                }
            ])
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_approved', true)])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with(['sections.lessons', 'instructor:id,name,avatar'])
            ->first()
        );

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $course->average_rating = $course->average_rating ? round($course->average_rating, 1) : null;

        $user = auth('sanctum')->user();

        if (Setting::get('track_course_views', true)) {
            CourseView::create([
                'course_id' => $course->id,
                'user_id' => $user?->id,
                'ip_address' => request()->ip(),
            ]);
        }
        $enrollment = $user ? Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first() : null;

        $isEnrolled = (bool) $enrollment;
        $accessExpired = $enrollment ? $enrollment->isExpired() : false;
        $hasAccess = $enrollment && in_array($enrollment->status, ['active', 'completed'], true) && !$accessExpired;

        $courseData = $course->toArray();
        $courseData['is_enrolled'] = $isEnrolled;
        $courseData['access_expired'] = $accessExpired;
        $courseData['access_expires_at'] = $enrollment?->expires_at?->toIso8601String();

        $courseData['sections'] = $course->sections->map(function ($section) use ($hasAccess) {
            $sectionData = $section->toArray();
            $sectionData['lessons'] = $section->lessons->map(function ($lesson) use ($hasAccess) {
                $lessonData = $lesson->toArray();

                $canWatch = $hasAccess || $lesson->is_preview;
                $lessonData['video_url'] = $canWatch ? $this->resolveVideoUrl($lesson) : null;
                unset($lessonData['video_path']);

                if (!$canWatch) {
                    $lessonData['content'] = null;
                    $lessonData['attachment'] = null;
                    $lessonData['attachment_name'] = null;
                }

                return $lessonData;
            });
            return $sectionData;
        });

        return ApiResponse::success($courseData, 'Course retrieved successfully');
    }

    private function resolveVideoUrl($lesson): ?string
    {
        if ($lesson->video_url) {
            return $lesson->video_url;
        }

        if ($lesson->video_path) {
            return Storage::disk('r2-private')->temporaryUrl($lesson->video_path, now()->addMinutes(30));
        }

        return null;
    }
}
