<?php

namespace Tests\Feature\Courses;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\LessonVideo;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LessonVideoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('r2-private');
        Storage::disk('r2-private')->buildTemporaryUrlsUsing(
            fn ($path, $expiration) => "https://fake-r2.test/{$path}"
        );
    }

    // ------------------------------------------------------------------
    // Course-scoped: /instructor/courses/{courseId}/sections/{sectionId}/lessons/{lessonId}/videos
    // ------------------------------------------------------------------

    public function test_guest_cannot_access_lesson_videos(): void
    {
        [, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        $response = $this->getJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos");

        $response->assertUnauthorized();
    }

    public function test_non_owner_instructor_cannot_list_lesson_videos(): void
    {
        [, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();
        $otherInstructor = $this->createVerifiedInstructor();

        Sanctum::actingAs($otherInstructor);

        $response = $this->getJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos");

        $response->assertForbidden();
    }

    public function test_instructor_can_list_lesson_videos_in_order(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=second', 'order' => 1]);
        LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=first', 'order' => 0]);

        Sanctum::actingAs($instructor);

        $response = $this->getJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.video_url', 'https://youtube.com/watch?v=first')
            ->assertJsonPath('data.1.video_url', 'https://youtube.com/watch?v=second');
    }

    public function test_instructor_can_add_video_by_url(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $response = $this->postJson(
            "/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos",
            ['video_url' => 'https://youtube.com/watch?v=abc123']
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.video_url', 'https://youtube.com/watch?v=abc123')
            ->assertJsonPath('data.video_source', 'https://youtube.com/watch?v=abc123');

        $this->assertDatabaseHas('lesson_videos', [
            'lesson_id' => $lesson->id,
            'video_url' => 'https://youtube.com/watch?v=abc123',
            'video_path' => null,
            'order' => 0,
        ]);
    }

    public function test_instructor_can_upload_video_file(): void
    {
        [$instructor, $course, $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $file = UploadedFile::fake()->create('lecture.mp4', 1000, 'video/mp4');

        $response = $this->postJson(
            "/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos",
            ['video' => $file, 'duration' => 120]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.duration', 120);

        $video = LessonVideo::first();
        $this->assertNotNull($video->video_path);
        $this->assertNull($video->video_url);
        Storage::disk('r2-private')->assertExists($video->video_path);
        $this->assertStringStartsWith("courses/{$course->id}/videos", $video->video_path);
    }

    public function test_video_order_increments_with_each_upload(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $this->postJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos", ['video_url' => 'https://youtube.com/watch?v=1'])
            ->assertJsonPath('data.order', 0);

        $this->postJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos", ['video_url' => 'https://youtube.com/watch?v=2'])
            ->assertJsonPath('data.order', 1);
    }

    public function test_upload_fails_without_file_or_url(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $response = $this->postJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos", []);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Provide a video file or a video URL.');
    }

    public function test_upload_fails_with_invalid_url(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $response = $this->postJson(
            "/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos",
            ['video_url' => 'not-a-url']
        );

        $response->assertStatus(422)->assertJsonPath('message', 'Validation failed');
    }

    public function test_upload_fails_when_course_pending_review(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson(status: Course::STATUS_PENDING);

        Sanctum::actingAs($instructor);

        $response = $this->postJson(
            "/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos",
            ['video_url' => 'https://youtube.com/watch?v=abc123']
        );

        $response->assertStatus(422)
            ->assertJsonPath('message', 'This course is pending review and cannot be edited until it is approved or rejected.');

        $this->assertDatabaseCount('lesson_videos', 0);
    }

    public function test_instructor_can_delete_video_and_its_file(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $path = "courses/{$courseId}/videos/lecture.mp4";
        Storage::disk('r2-private')->put($path, 'fake-contents');
        $video = LessonVideo::create(['lesson_id' => $lesson->id, 'video_path' => $path, 'order' => 0]);

        $response = $this->deleteJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos/{$video->id}");

        $response->assertOk()->assertJsonPath('success', true);

        $this->assertSoftDeleted('lesson_videos', ['id' => $video->id]);
        Storage::disk('r2-private')->assertMissing($path);
    }

    public function test_deleting_nonexistent_video_returns_404(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();

        Sanctum::actingAs($instructor);

        $response = $this->deleteJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos/999999");

        $response->assertStatus(404)->assertJsonPath('message', 'Video not found');
    }

    public function test_delete_fails_when_course_pending_review(): void
    {
        [$instructor, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson(status: Course::STATUS_PENDING);
        $video = LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=abc', 'order' => 0]);

        Sanctum::actingAs($instructor);

        $response = $this->deleteJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos/{$video->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('lesson_videos', ['id' => $video->id, 'deleted_at' => null]);
    }

    public function test_non_owner_instructor_cannot_delete_video(): void
    {
        [, , $lesson, $courseId, $sectionId] = $this->createCourseWithLesson();
        $video = LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=abc', 'order' => 0]);
        $otherInstructor = $this->createVerifiedInstructor();

        Sanctum::actingAs($otherInstructor);

        $response = $this->deleteJson("/api/v1/instructor/courses/{$courseId}/sections/{$sectionId}/lessons/{$lesson->id}/videos/{$video->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('lesson_videos', ['id' => $video->id, 'deleted_at' => null]);
    }

    // ------------------------------------------------------------------
    // Section-scoped (standalone sections not yet attached to a course):
    // /instructor/sections/{sectionId}/lessons/{lessonId}/videos
    // ------------------------------------------------------------------

    public function test_instructor_can_upload_video_to_standalone_section_lesson(): void
    {
        [$instructor, $section, $lesson] = $this->createStandaloneSectionWithLesson();

        Sanctum::actingAs($instructor);

        $response = $this->postJson(
            "/api/v1/instructor/sections/{$section->id}/lessons/{$lesson->id}/videos",
            ['video_url' => 'https://youtube.com/watch?v=standalone']
        );

        $response->assertCreated()->assertJsonPath('data.video_url', 'https://youtube.com/watch?v=standalone');

        $this->assertDatabaseHas('lesson_videos', [
            'lesson_id' => $lesson->id,
            'video_url' => 'https://youtube.com/watch?v=standalone',
        ]);
    }

    public function test_standalone_section_video_upload_uses_section_directory(): void
    {
        [$instructor, $section, $lesson] = $this->createStandaloneSectionWithLesson();

        Sanctum::actingAs($instructor);

        $file = UploadedFile::fake()->create('lecture.mp4', 500, 'video/mp4');

        $this->postJson(
            "/api/v1/instructor/sections/{$section->id}/lessons/{$lesson->id}/videos",
            ['video' => $file]
        )->assertCreated();

        $video = LessonVideo::first();
        $this->assertStringStartsWith("sections/{$section->id}/videos", $video->video_path);
    }

    public function test_instructor_can_list_and_delete_video_on_standalone_section(): void
    {
        [$instructor, $section, $lesson] = $this->createStandaloneSectionWithLesson();
        $video = LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=abc', 'order' => 0]);

        Sanctum::actingAs($instructor);

        $this->getJson("/api/v1/instructor/sections/{$section->id}/lessons/{$lesson->id}/videos")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->deleteJson("/api/v1/instructor/sections/{$section->id}/lessons/{$lesson->id}/videos/{$video->id}")
            ->assertOk();

        $this->assertSoftDeleted('lesson_videos', ['id' => $video->id]);
    }

    public function test_non_owner_cannot_manage_standalone_section_videos(): void
    {
        [, $section, $lesson] = $this->createStandaloneSectionWithLesson();
        $otherInstructor = $this->createVerifiedInstructor();

        Sanctum::actingAs($otherInstructor);

        $this->getJson("/api/v1/instructor/sections/{$section->id}/lessons/{$lesson->id}/videos")
            ->assertForbidden();

        $this->postJson("/api/v1/instructor/sections/{$section->id}/lessons/{$lesson->id}/videos", ['video_url' => 'https://youtube.com/watch?v=x'])
            ->assertForbidden();
    }

    // ------------------------------------------------------------------
    // LessonVideo model
    // ------------------------------------------------------------------

    public function test_video_source_prefers_video_url(): void
    {
        $video = new LessonVideo(['video_url' => 'https://youtube.com/watch?v=abc', 'video_path' => 'courses/1/videos/a.mp4']);

        $this->assertSame('https://youtube.com/watch?v=abc', $video->video_source);
    }

    public function test_video_source_falls_back_to_temporary_url_for_uploaded_file(): void
    {
        $video = new LessonVideo(['video_path' => 'courses/1/videos/a.mp4']);

        $this->assertSame('https://fake-r2.test/courses/1/videos/a.mp4', $video->video_source);
    }

    public function test_video_source_is_null_without_url_or_path(): void
    {
        $video = new LessonVideo();

        $this->assertNull($video->video_source);
    }

    public function test_lesson_videos_relation_is_ordered(): void
    {
        [, , $lesson] = $this->createCourseWithLesson();

        LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=b', 'order' => 2]);
        LessonVideo::create(['lesson_id' => $lesson->id, 'video_url' => 'https://youtube.com/watch?v=a', 'order' => 1]);

        $ordered = $lesson->videos()->pluck('video_url')->all();

        $this->assertSame([
            'https://youtube.com/watch?v=a',
            'https://youtube.com/watch?v=b',
        ], $ordered);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function createVerifiedInstructor(): User
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        InstructorVerification::create([
            'user_id' => $instructor->id,
            'status' => 'approved',
        ]);

        return $instructor;
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson, 3: int, 4: int}
     */
    private function createCourseWithLesson(string $status = Course::STATUS_DRAFT): array
    {
        $instructor = $this->createVerifiedInstructor();

        $category = Category::create(['name' => 'Programming', 'slug' => 'programming-' . uniqid()]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Test Course',
            'slug' => 'test-course-' . uniqid(),
            'price' => 0,
            'status' => $status,
            'is_published' => $status === Course::STATUS_PUBLISHED,
        ]);

        $section = Section::create([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Lesson 1',
            'type' => Lesson::TYPE_VIDEO,
            'order' => 0,
        ]);

        return [$instructor, $course, $lesson, $course->id, $section->id];
    }

    /**
     * @return array{0: User, 1: Section, 2: Lesson}
     */
    private function createStandaloneSectionWithLesson(): array
    {
        $instructor = $this->createVerifiedInstructor();

        $section = Section::create([
            'course_id' => null,
            'instructor_id' => $instructor->id,
            'title' => 'Standalone Section',
            'order' => 0,
        ]);

        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Lesson 1',
            'type' => Lesson::TYPE_VIDEO,
            'order' => 0,
        ]);

        return [$instructor, $section, $lesson];
    }
}
