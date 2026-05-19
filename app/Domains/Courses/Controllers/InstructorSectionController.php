<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Section;
use App\Domains\Courses\Models\Course;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class InstructorSectionController extends Controller
{
    public function index($courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $sections = Section::where('course_id', $courseId)
            ->orderBy('order')
            ->get();

        return ApiResponse::success($sections, 'Sections retrieved successfully');
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section = Section::create([
            'course_id' => $courseId,
            'title' => $validated['title'],
            'order' => Section::where('course_id', $courseId)->count() + 1,
        ]);

        return ApiResponse::success($section, 'Section created successfully', 201);
    }

    public function update(Request $request, $courseId, $sectionId)
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
        ]);

        $section->update($validated);

        return ApiResponse::success($section, 'Section updated successfully');
    }

    public function destroy($courseId, $sectionId)
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

        $section->delete();

        return ApiResponse::success(null, 'Section deleted successfully');
    }
}
