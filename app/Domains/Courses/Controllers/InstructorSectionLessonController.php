<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\System\Models\Setting;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Section-scoped lesson management — works for both standalone sections
// (course_id null) and course-attached sections, so the frontend can manage
// lessons on a section before it's ever attached to a course.
class InstructorSectionLessonController extends Controller
{
    public function index($sectionId)
    {
        $section = $this->findOwnedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        $lessons = Lesson::where('section_id', $sectionId)
            ->withCount('videos')
            ->orderBy('order')
            ->get();

        return ApiResponse::success($lessons, 'Lessons retrieved successfully');
    }

    public function store(Request $request, $sectionId)
    {
        $section = $this->findOwnedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        if ($locked = $this->pendingReviewError($section)) {
            return $locked;
        }

        if ($section->course_id) {
            $maxLessons = (int) Setting::get('max_lessons_per_course', 200);
            if ($maxLessons > 0 && $section->course->lessons()->count() >= $maxLessons) {
                return ApiResponse::error("This course has reached the maximum of {$maxLessons} lessons.", 422);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article',
            'description' => 'nullable|string|max:1000',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson = Lesson::create([
            'section_id' => $sectionId,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'is_preview' => $validated['is_preview'] ?? false,
            'order' => Lesson::where('section_id', $sectionId)->count() + 1,
        ]);

        return ApiResponse::success($lesson, 'Lesson created successfully', 201);
    }

    public function update(Request $request, $sectionId, $lessonId)
    {
        $section = $this->findOwnedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        $lesson = Lesson::where('id', $lessonId)
            ->where('section_id', $sectionId)
            ->first();

        if (!$lesson) {
            return ApiResponse::error('Lesson not found', 404);
        }

        if ($locked = $this->pendingReviewError($section)) {
            return $locked;
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'nullable|in:video,article',
            'description' => 'nullable|string|max:1000',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson->update($validated);

        return ApiResponse::success($lesson, 'Lesson updated successfully');
    }

    public function uploadVideo(Request $request, $sectionId, $lessonId)
    {
        $section = $this->findOwnedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        $lesson = Lesson::where('id', $lessonId)
            ->where('section_id', $sectionId)
            ->first();

        if (!$lesson) {
            return ApiResponse::error('Lesson not found', 404);
        }

        if ($locked = $this->pendingReviewError($section)) {
            return $locked;
        }

        $allowedFormats = Setting::get('allowed_video_formats', 'mp4,mov,avi,webm');
        $maxSizeKb = (int) Setting::get('max_video_upload_size', 512000);

        $request->validate([
            'video' => "required|file|mimes:{$allowedFormats}|max:{$maxSizeKb}",
        ]);

        if ($lesson->video_path) {
            Storage::disk('r2-private')->delete($lesson->video_path);
        }

        // Standalone sections have no course yet, so videos live under the
        // section until the section is attached to a course.
        $directory = $section->course_id
            ? "courses/{$section->course_id}/videos"
            : "sections/{$sectionId}/videos";

        $path = $request->file('video')->store($directory, 'r2-private');

        $lesson->update([
            'video_path' => $path,
            'video_url'  => null,
        ]);

        return ApiResponse::success([
            'video_path' => $path,
            'video_url'  => Storage::disk('r2-private')->temporaryUrl($path, now()->addMinutes(30)),
        ], 'Video uploaded successfully');
    }

    public function destroy($sectionId, $lessonId)
    {
        $section = $this->findOwnedSection($sectionId);

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        $lesson = Lesson::where('id', $lessonId)
            ->where('section_id', $sectionId)
            ->first();

        if (!$lesson) {
            return ApiResponse::error('Lesson not found', 404);
        }

        if ($locked = $this->pendingReviewError($section)) {
            return $locked;
        }

        if ($section->course_id && $section->course?->isPublished()) {
            return ApiResponse::error('This course is public, so its content can\'t be deleted.', 422);
        }

        $lesson->delete();

        return ApiResponse::success(null, 'Lesson deleted successfully');
    }

    // A section is "owned" by the authenticated instructor either directly
    // (standalone, course_id null) or via its course.
    private function findOwnedSection($sectionId): ?Section
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

    // Standalone sections (no course yet) are always editable. Once attached
    // to a course, lesson edits are locked while that course awaits review —
    // same rule as editing the course/section directly.
    private function pendingReviewError(Section $section): ?\Illuminate\Http\JsonResponse
    {
        if ($section->course_id && $section->course?->isPendingReview()) {
            return ApiResponse::error('This course is pending review and cannot be edited until it is approved or rejected.', 422);
        }

        return null;
    }
}
