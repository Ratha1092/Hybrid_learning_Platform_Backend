<?php

namespace App\Domains\Courses\Services;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Courses\Models\Course;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function create(array $data, int $userId): Course
    {
        $attempts = 0;
        $slug = $this->generateUniqueSlug($data['title']);

        while (true) {
            try {
                return DB::transaction(function () use ($data, $userId, $slug) {
                    $course = Course::create([
                        ...$data,
                        'instructor_id' => $userId,
                        'slug' => $slug,
                        'status' => 'draft',
                        'is_published' => false,
                        'level' => $data['level'] ?? 'beginner',
                        'language' => $data['language'] ?? 'English',
                    ]);

                    Cache::forget('courses.published');
                    ActivityLogService::logChange('course.created', $course);
                    return $course;
                });
            } catch (UniqueConstraintViolationException) {
                if (++$attempts >= 5) {
                    throw new \RuntimeException('Failed to create course. Please try again.');
                }
                $slug = $this->generateUniqueSlug($data['title']) . '-' . substr(uniqid(), -5);
            }
        }
    }

    public function update(Course $course, array $data, ?UploadedFile $thumbnail = null): Course
    {
        $attempts = 0;
        $slug = isset($data['title']) ? $this->generateUniqueSlug($data['title'], $course->id) : null;

        while (true) {
            try {
                return DB::transaction(function () use ($course, $data, $thumbnail, $slug) {
                    if ($slug !== null) {
                        $data['slug'] = $slug;
                    }

                    if ($thumbnail) {
                        if ($course->thumbnail) {
                            Storage::disk('public')->delete($course->thumbnail);
                        }
                        $data['thumbnail'] = $thumbnail->store('courses/thumbnails', 'public');
                    }

                    unset($data['thumbnail_file']);
                    $course->update($data);
                    Cache::forget('courses.published');
                    Cache::forget("courses.slug.{$course->slug}");

                    return $course->fresh();
                });
            } catch (UniqueConstraintViolationException) {
                if (++$attempts >= 5) {
                    throw new \RuntimeException('Failed to update course. Please try again.');
                }
                $slug = $this->generateUniqueSlug($data['title'], $course->id) . '-' . substr(uniqid(), -5);
            }
        }
    }

    public function delete(Course $course): void
    {
        DB::transaction(function () use ($course) {
            Cache::forget('courses.published');
            Cache::forget("courses.slug.{$course->slug}");
            $course->delete();
        });
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (
            Course::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }
}