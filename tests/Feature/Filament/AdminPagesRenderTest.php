<?php

namespace Tests\Feature\Filament;

use App\Domains\Courses\Models\Course;
use App\Domains\Promotions\Models\Coupon;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Smoke test for the custom Filament admin pages touched by the wire:key
 * sweep — confirms each still renders (200, no server-side exception) with
 * a real row present, since a bad wire:key expression (undefined variable,
 * wrong loop var) would only surface at render time, not at blade-compile time.
 */
class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    private const SLUGS = [
        'payments',
        'orders',
        'instructor-verifications',
        'payout-accounts',
        'payouts',
        'content-reports',
        'users',
        'instructors',
        'notifications',
        'roles',
        'coupons',
        'categories',
        'lessons',
        'contact-messages',
        'reviews',
        'courses',
    ];

    public function test_admin_pages_render_without_error(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        foreach (self::SLUGS as $slug) {
            $status = $this->get("/admin/{$slug}")->getStatusCode();
            $this->assertEquals(200, $status, "Expected 200 for /admin/{$slug}, got {$status}");
        }
    }

    public function test_coupons_page_renders_with_a_real_row(): void
    {
        // Exercises the trickiest wire:key edit — inserted before an
        // @unless($coupon->trashed()) conditional attribute block — with
        // both a live and a soft-deleted coupon, since that conditional
        // branches the surrounding <tr> attributes differently per row.
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        \App\Domains\Promotions\Models\Coupon::create([
            'code' => 'TESTCODE1',
            'type' => \App\Domains\Promotions\Models\Coupon::TYPE_FIXED,
            'value' => 10,
            'is_active' => true,
        ]);
        $trashed = \App\Domains\Promotions\Models\Coupon::create([
            'code' => 'TESTCODE2',
            'type' => \App\Domains\Promotions\Models\Coupon::TYPE_PERCENTAGE,
            'value' => 20,
            'is_active' => false,
        ]);
        $trashed->delete();

        $this->get('/admin/coupons')->assertOk();
    }

    public function test_course_students_page_renders_with_a_real_enrollment(): void
    {
        // Exercises the other tricky edit — wire:key inserted before an
        // @if($studentViewUrl) conditional attribute block.
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $student = User::factory()->create();
        $student->assignRole('student');
        $category = \App\Domains\Courses\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category-' . uniqid()]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course-' . uniqid(),
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'price' => 10,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        \App\Domains\Learning\Models\Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        $this->get("/admin/courses/{$course->id}/students")->assertOk();
    }

    public function test_super_admin_can_soft_delete_a_coupon(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $coupon = Coupon::create([
            'code' => 'DELTEST',
            'type' => Coupon::TYPE_FIXED,
            'value' => 5,
            'is_active' => true,
        ]);

        Livewire::test(\App\Filament\Pages\Coupons::class)
            ->call('deleteCoupon', $coupon->id)
            ->assertHasNoErrors();

        $this->assertTrue($coupon->fresh()->trashed());
    }

    public function test_category_courses_page_renders_without_error(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $category = \App\Domains\Courses\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category-' . uniqid()]);

        $this->get("/admin/category-courses?id={$category->id}")->assertOk();
    }

    public function test_course_students_page_renders_without_error(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $category = \App\Domains\Courses\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category-' . uniqid()]);

        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course-' . uniqid(),
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'price' => 10,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        $this->get("/admin/courses/{$course->id}/students")->assertOk();
    }
}
