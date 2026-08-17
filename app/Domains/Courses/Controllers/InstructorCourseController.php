<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Section;
use App\Domains\Courses\Services\CourseService;
use App\Domains\Notifications\Notifications\AdminCourseSubmittedNotification;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Jobs\Notifications\NotifyAdminsJob;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InstructorCourseController extends Controller
{
    public function __construct(
        protected CourseService $courseService
    ) {}
    public function index(): JsonResponse
    {
        $courses = Course::query()
            ->where('instructor_id', auth()->id())
            ->withCount(['enrollments as student_count' => fn ($q) => $q->whereIn('status', ['active', 'completed'])])
            ->latest()
            ->get();

        return ApiResponse::success(
            $courses,'Courses retrieved successfully'
        );
    }
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', static::minPriceRule()],
            'level' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $course = $this->courseService->create(
            $validated,
            auth()->id()
        );

        return ApiResponse::success($course,'Course created successfully',201);
    }
    public function show(int $id): JsonResponse
    {
        $course = Course::query()
            ->where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found',404);
        }
        return ApiResponse::success($course,'Course retrieved successfully');
    }
    public function update(
        Request $request,
        int $id
    ): JsonResponse {

        $course = Course::query()
            ->where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found',404
            );
        }

        if ($course->isPendingReview()) {
            return ApiResponse::error('This course is pending review and cannot be edited until it is approved or rejected.', 422);
        }

        $validated = $request->validate([
            'title'               => ['sometimes', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'short_description'   => ['nullable', 'string', 'max:500'],
            'price'               => ['sometimes', 'numeric', static::minPriceRule()],
            'level'               => ['nullable', 'string'],
            'language'            => ['nullable', 'string'],
            'category_id'         => ['sometimes', 'exists:categories,id'],
            'requirements'        => ['nullable', 'string'],
            'what_you_will_learn' => ['nullable', 'string'],
            'visibility'          => ['sometimes', 'in:public,private'],
            'certificate_enabled' => ['sometimes', 'boolean'],
            'thumbnail'           => [
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp',
                'max:' . (int) Setting::get('max_course_thumbnail_size', 2048),
            ],
        ]);

        $updatedCourse = $this->courseService->update($course, $validated, $request->file('thumbnail'));
        return ApiResponse::success(
            $updatedCourse,
            'Course updated successfully'
        );
    }
    public function destroy(int $id): JsonResponse
    {
        $course = Course::query()
            ->where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found',404
            );
        }

        $this->courseService->delete($course);
        return ApiResponse::success(
            null,
            'Course deleted successfully'
        );
    }
    public function attachSections(Request $request, int $id): JsonResponse
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $validated = $request->validate([
            'section_ids'   => 'required|array|min:1',
            'section_ids.*' => 'integer|exists:sections,id',
        ]);

        $count = Section::whereIn('id', $validated['section_ids'])
            ->where('instructor_id', auth()->id())
            ->whereNull('course_id')
            ->update(['course_id' => $id]);

        return ApiResponse::success([
            'course_id'      => $id,
            'attached_count' => $count,
        ], 'Sections attached successfully');
    }

    public function submitForReview(int $id): JsonResponse
    {
        $course = Course::query()
            ->where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found',404
            );
        }

        if (!$course->canBePublished()) {
            return ApiResponse::error('Course must contain at least one section.',400
            );
        }

        if ($course->isPublished()) {
            return ApiResponse::error('Course is already published.',400);
        }

        $isFree = (float) $course->price <= 0.0;

        $autoApprove = ($isFree && Setting::get('free_course_auto_approval', false))
            || (Setting::get('course_auto_approval', false) && !Setting::get('course_review_required', true));

        if ($autoApprove) {
            $course->publish((int) $course->instructor_id);

            return ApiResponse::success(
                $course,
                'Course published automatically.'
            );
        }

        $course->submitForReview();

        NotifyAdminsJob::dispatch(
            AdminCourseSubmittedNotification::class,
            [$course->id, $course->title, auth()->user()->name],
            'courses.approve'
        );

        return ApiResponse::success(
            $course,
            'Course submitted for review successfully.'
        );
    }

    private static function minPriceRule(): string
    {
        return Setting::get('allow_free_courses', true) ? 'min:0' : 'min:0.01';
    }
}