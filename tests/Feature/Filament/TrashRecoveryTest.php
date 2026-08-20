<?php

namespace Tests\Feature\Filament;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use App\Filament\Pages\Categories;
use App\Filament\Pages\Courses as CoursesPage;
use App\Filament\Pages\Lessons;
use App\Filament\Pages\Sections;
use App\Filament\Pages\Users as UsersPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Every one of these models already had a working soft-delete, but no
 * reachable UI to recover from it — the native Filament Resources' Restore
 * actions existed in code but their list routes were never registered.
 * This exercises the newly-added "Deleted" tab + restore on each custom page.
 */
class TrashRecoveryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        return $admin;
    }

    public function test_category_trashed_tab_renders_and_restores(): void
    {
        $this->admin();

        $category = Category::create(['name' => 'Old Cat', 'slug' => 'old-cat-' . uniqid()]);
        $category->delete();

        $this->assertTrue($category->trashed());

        Livewire::test(Categories::class)
            ->set('status', 'trashed')
            ->assertOk()
            ->assertSee('Old Cat')
            ->call('restoreCategory', $category->id)
            ->assertHasNoErrors();

        $this->assertFalse($category->fresh()->trashed());
    }

    public function test_category_force_delete_permanently_removes_it(): void
    {
        $this->admin();

        $category = Category::create(['name' => 'Gone Forever', 'slug' => 'gone-forever-' . uniqid()]);
        $category->delete();

        Livewire::test(Categories::class)
            ->set('status', 'trashed')
            ->call('forceDeleteCategory', $category->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_section_trashed_tab_renders_and_restores(): void
    {
        $this->admin();

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);
        $course = Course::create([
            'title' => 'Course', 'slug' => 'course-' . uniqid(),
            'instructor_id' => $instructor->id, 'category_id' => $category->id,
            'price' => 10, 'status' => Course::STATUS_PUBLISHED, 'is_published' => true,
        ]);
        $section = Section::create(['title' => 'Old Section', 'course_id' => $course->id, 'order' => 1]);
        $section->delete();

        Livewire::test(Sections::class)
            ->set('status', 'trashed')
            ->assertOk()
            ->assertSee('Old Section')
            ->call('restoreSection', $section->id)
            ->assertHasNoErrors();

        $this->assertFalse($section->fresh()->trashed());
    }

    public function test_lesson_trashed_tab_renders_and_restores(): void
    {
        $this->admin();

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);
        $course = Course::create([
            'title' => 'Course', 'slug' => 'course-' . uniqid(),
            'instructor_id' => $instructor->id, 'category_id' => $category->id,
            'price' => 10, 'status' => Course::STATUS_PUBLISHED, 'is_published' => true,
        ]);
        $section = Section::create(['title' => 'Section', 'course_id' => $course->id, 'order' => 1]);
        $lesson = Lesson::create(['title' => 'Old Lesson', 'section_id' => $section->id, 'type' => 'video', 'order' => 1]);
        $lesson->delete();

        Livewire::test(Lessons::class)
            ->set('tab', 'trashed')
            ->assertOk()
            ->assertSee('Old Lesson')
            ->call('restoreLesson', $lesson->id)
            ->assertHasNoErrors();

        $this->assertFalse($lesson->fresh()->trashed());
    }

    public function test_user_trashed_tab_renders_and_restores(): void
    {
        $this->admin();

        $student = User::factory()->create(['name' => 'Old Student']);
        $student->assignRole('student');
        $student->delete();

        Livewire::test(UsersPage::class)
            ->set('tab', 'trashed')
            ->assertOk()
            ->assertSee('Old Student')
            ->call('restoreUser', $student->id)
            ->assertHasNoErrors();

        $this->assertFalse($student->fresh()->trashed());
    }

    public function test_course_trashed_tab_renders_and_restores(): void
    {
        $this->admin();

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);
        $course = Course::create([
            'title' => 'Old Course', 'slug' => 'old-course-' . uniqid(),
            'instructor_id' => $instructor->id, 'category_id' => $category->id,
            'price' => 10, 'status' => Course::STATUS_PUBLISHED, 'is_published' => true,
        ]);
        $course->delete();

        Livewire::test(CoursesPage::class)
            ->set('activeTab', 'trashed')
            ->assertOk()
            ->assertSee('Old Course')
            ->call('restoreCourse', $course->id)
            ->assertHasNoErrors();

        $fresh = $course->fresh();
        $this->assertFalse($fresh->trashed());
        $this->assertEquals(Course::STATUS_PUBLISHED, $fresh->status);
    }
}
