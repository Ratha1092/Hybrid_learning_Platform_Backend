<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\LessonAttachment;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorLessonResourceController extends Controller
{
    public function index(int $courseId, int $sectionId, int $lessonId): JsonResponse
    {
        if (!$this->instructorOwnsCourse($courseId)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $resources = LessonAttachment::where('lesson_id', $lessonId)->get();

        return ApiResponse::success($resources, 'Resources retrieved successfully');
    }

    public function store(Request $request, int $courseId, int $sectionId, int $lessonId): JsonResponse
    {
        if (!$this->instructorOwnsCourse($courseId)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|file|mimes:pdf,zip,doc,docx,ppt,pptx,mp4,jpg,png|max:51200',
        ]);

        $path = $request->file('file')->store(
            "courses/{$courseId}/resources", 'r2'
        );

        $resource = LessonAttachment::create([
            'lesson_id' => $lessonId,
            'title'     => $validated['title'],
            'type'      => $request->file('file')->getClientOriginalExtension(),
            'file_path' => $path,
        ]);

        return ApiResponse::success($resource, 'Resource uploaded successfully', 201);
    }

    public function destroy(int $courseId, int $sectionId, int $lessonId, int $resourceId): JsonResponse
    {
        if (!$this->instructorOwnsCourse($courseId)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $resource = LessonAttachment::where('id', $resourceId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$resource) {
            return ApiResponse::error('Resource not found', 404);
        }

        \Storage::disk('r2')->delete($resource->file_path);
        $resource->delete();

        return ApiResponse::success(null, 'Resource deleted successfully');
    }

    private function instructorOwnsCourse(int $courseId): bool
    {
        return Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->exists();
    }
}
