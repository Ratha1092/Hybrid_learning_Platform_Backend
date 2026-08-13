<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\LessonAttachment;
use App\Domains\Courses\Models\Section;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Section-scoped equivalent of InstructorLessonResourceController — lets
// resources be attached to lessons on a standalone (not-yet-attached) section.
class InstructorSectionLessonResourceController extends Controller
{
    public function index(int $sectionId, int $lessonId): JsonResponse
    {
        if (!$this->instructorOwnsSection($sectionId)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $resources = LessonAttachment::where('lesson_id', $lessonId)->get();

        return ApiResponse::success($resources, 'Resources retrieved successfully');
    }

    public function store(Request $request, int $sectionId, int $lessonId): JsonResponse
    {
        $section = $this->ownedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|file|mimes:pdf,zip,doc,docx,ppt,pptx,mp4,jpg,png|max:51200',
        ]);

        $directory = $section->course_id
            ? "courses/{$section->course_id}/resources"
            : "sections/{$sectionId}/resources";

        $path = $request->file('file')->store($directory, 'r2-private');

        $resource = LessonAttachment::create([
            'lesson_id' => $lessonId,
            'title'     => $validated['title'],
            'type'      => $request->file('file')->getClientOriginalExtension(),
            'file_path' => $path,
        ]);

        return ApiResponse::success($resource, 'Resource uploaded successfully', 201);
    }

    public function destroy(int $sectionId, int $lessonId, int $resourceId): JsonResponse
    {
        if (!$this->instructorOwnsSection($sectionId)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $resource = LessonAttachment::where('id', $resourceId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$resource) {
            return ApiResponse::error('Resource not found', 404);
        }

        \Storage::disk('r2-private')->delete($resource->file_path);
        $resource->delete();

        return ApiResponse::success(null, 'Resource deleted successfully');
    }

    private function instructorOwnsSection(int $sectionId): bool
    {
        return $this->ownedSection($sectionId) !== null;
    }

    private function ownedSection(int $sectionId): ?Section
    {
        $section = Section::where('id', $sectionId)->first();

        if (!$section) {
            return null;
        }

        $ownerId = $section->course_id
            ? $section->course?->instructor_id
            : $section->instructor_id;

        return $ownerId === auth()->id() ? $section : null;
    }
}
