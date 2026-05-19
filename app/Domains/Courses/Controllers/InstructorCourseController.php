<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Services\CourseService;

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
            'price' => ['required', 'numeric', 'min:0'],
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

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'level' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
            'category_id' => ['sometimes', 'exists:categories,id'],
        ]);

        $updatedCourse = $this->courseService->update($course,$validated
        );
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
        $course->submitForReview();

        return ApiResponse::success(
            $course,
            'Course submitted for review successfully.'
        );
    }
}