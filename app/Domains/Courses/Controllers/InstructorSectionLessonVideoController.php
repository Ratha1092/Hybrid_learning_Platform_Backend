<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\LessonVideo;
use App\Domains\Courses\Models\Section;
use App\Domains\System\Models\Setting;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Section-scoped equivalent of InstructorLessonVideoController — lets videos
// be attached to lessons on a standalone (not-yet-attached) section.
class InstructorSectionLessonVideoController extends Controller
{
    public function index(int $sectionId, int $lessonId): JsonResponse
    {
        if (!$this->instructorOwnsSection($sectionId)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $videos = LessonVideo::where('lesson_id', $lessonId)->orderBy('order')->get();

        return ApiResponse::success($videos, 'Videos retrieved successfully');
    }

    public function store(Request $request, int $sectionId, int $lessonId): JsonResponse
    {
        $section = $this->ownedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Unauthorized', 403);
        }

        if ($section->course_id && $section->course?->isPendingReview()) {
            return ApiResponse::error('This course is pending review and cannot be edited until it is approved or rejected.', 422);
        }

        $allowedFormats = Setting::get('allowed_video_formats', 'mp4,mov,avi,webm');
        $maxSizeKb = (int) Setting::get('max_video_upload_size', 512000);

        $validated = $request->validate([
            'video' => "nullable|file|mimes:{$allowedFormats}|max:{$maxSizeKb}",
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
        ]);

        if (!$request->hasFile('video') && empty($validated['video_url'] ?? null)) {
            return ApiResponse::error('Provide a video file or a video URL.', 422);
        }

        $directory = $section->course_id
            ? "courses/{$section->course_id}/videos"
            : "sections/{$sectionId}/videos";

        $path = null;
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store($directory, 'r2-private');
        }

        $video = LessonVideo::create([
            'lesson_id' => $lessonId,
            'video_path' => $path,
            'video_url' => $path ? null : $validated['video_url'],
            'duration' => $validated['duration'] ?? null,
            'order' => LessonVideo::where('lesson_id', $lessonId)->count(),
        ]);

        return ApiResponse::success($video, 'Video uploaded successfully', 201);
    }

    public function destroy(int $sectionId, int $lessonId, int $videoId): JsonResponse
    {
        $section = $this->ownedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Unauthorized', 403);
        }

        if ($section->course_id && $section->course?->isPendingReview()) {
            return ApiResponse::error('This course is pending review and cannot be edited until it is approved or rejected.', 422);
        }

        if ($section->course_id && $section->course?->isPublished()) {
            return ApiResponse::error('This course is public, so its content can\'t be deleted.', 422);
        }

        $video = LessonVideo::where('id', $videoId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$video) {
            return ApiResponse::error('Video not found', 404);
        }

        if ($video->video_path) {
            \Storage::disk('r2-private')->delete($video->video_path);
        }
        $video->delete();

        return ApiResponse::success(null, 'Video deleted successfully');
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
