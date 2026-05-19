<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class InstructorLessonController extends Controller
{
    public function index($courseId, $sectionId)
    {
        $section = Section::where('id', $sectionId)
            ->where('course_id', $courseId)
            ->first();

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        if ($section->course->instructor_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $lessons = Lesson::where('section_id', $sectionId)
            ->orderBy('order')
            ->get();

        return ApiResponse::success($lessons, 'Lessons retrieved successfully');
    }

    public function store(Request $request, $courseId, $sectionId)
    {
        $section = Section::where('id', $sectionId)
            ->where('course_id', $courseId)
            ->first();

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        if ($section->course->instructor_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson = Lesson::create([
            'section_id' => $sectionId,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'is_preview' => $validated['is_preview'] ?? false,
            'order' => Lesson::where('section_id', $sectionId)->count() + 1,
        ]);

        return ApiResponse::success($lesson, 'Lesson created successfully', 201);
    }

    public function update(Request $request, $courseId, $sectionId, $lessonId)
    {
        $lesson = Lesson::where('id', $lessonId)
            ->where('section_id', $sectionId)
            ->whereHas('section', fn($query) => $query->where('course_id', $courseId))
            ->first();

        if (!$lesson) {
            return ApiResponse::error('Lesson not found', 404);
        }

        if ($lesson->section->course->instructor_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'nullable|in:video,article,quiz',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson->update($validated);

        return ApiResponse::success($lesson, 'Lesson updated successfully');
    }

    public function destroy($courseId, $sectionId, $lessonId)
    {
        $lesson = Lesson::where('id', $lessonId)
            ->where('section_id', $sectionId)
            ->whereHas('section', fn($query) => $query->where('course_id', $courseId))
            ->first();

        if (!$lesson) {
            return ApiResponse::error('Lesson not found', 404);
        }

        if ($lesson->section->course->instructor_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $lesson->delete();

        return ApiResponse::success(null, 'Lesson deleted successfully');
    }
}
