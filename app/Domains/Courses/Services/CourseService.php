<?php

namespace App\Domains\Courses\Services;

use App\Domains\Courses\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function create(array $data, int $userId): Course
    {
        return DB::transaction(function () use ($data, $userId) {

            $slug = $this->generateUniqueSlug($data['title']);

            return Course::create([
                ...$data,
                'instructor_id' => $userId,
                'slug' => $slug,
                'status' => 'draft',
                'is_published' => false,
                'level' => $data['level'] ?? 'beginner',
                'language' => $data['language'] ?? 'English',
            ]);
        });
    }

    public function update(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {

            if (isset($data['title'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $course->id);
            }

            $course->update($data);

            return $course;
        });
    }

    public function delete(Course $course): void
    {
        DB::transaction(function () use ($course) {
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