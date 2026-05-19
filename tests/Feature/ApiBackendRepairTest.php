<?php

namespace Tests\Feature;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Section;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBackendRepairTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_logout_through_api(): void
    {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $register
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'user']]);

        $token = $register->json('data.token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_password_reset_api_accepts_generated_token_once(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('OldPassword1!'),
        ]);

        $forgot = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $forgot->assertOk()->assertJsonPath('success', true);

        $token = $forgot->json('data.token');

        $this->postJson('/api/v1/auth/reset-password/verify', [
            'token' => $token,
        ])->assertOk();

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertOk();

        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));

        $this->postJson('/api/v1/auth/reset-password/verify', [
            'token' => $token,
        ])->assertBadRequest();
    }

    public function test_email_verification_api_uses_plain_token_from_send_response(): void
    {
        $user = User::factory()->unverified()->create();

        Sanctum::actingAs($user);

        $send = $this->postJson('/api/v1/auth/email/send');

        $send->assertOk()->assertJsonPath('success', true);

        $this->postJson('/api/v1/auth/email/verify', [
            'token' => $send->json('data.token'),
        ])->assertOk();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verified_instructor_can_manage_nested_sections_and_lessons(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'verified',
        ]);
        $category = Category::create(['name' => 'Development', 'slug' => 'development']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel APIs',
            'slug' => 'laravel-apis',
            'price' => 20,
            'status' => 'draft',
            'is_published' => false,
        ]);
        $section = Section::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'order' => 1,
        ]);

        Sanctum::actingAs($instructor);

        $lesson = $this->postJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons", [
            'title' => 'Intro',
            'type' => 'video',
            'video_url' => 'https://example.com/intro.mp4',
            'is_preview' => true,
        ]);

        $lesson
            ->assertCreated()
            ->assertJsonPath('data.type', 'video')
            ->assertJsonPath('data.is_preview', true);
    }

    public function test_orders_are_created_from_course_id_and_scoped_to_owner(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'verified',
        ]);
        $category = Category::create(['name' => 'Business', 'slug' => 'business']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Business Basics',
            'slug' => 'business-basics',
            'price' => 30,
            'status' => 'published',
            'is_published' => true,
        ]);

        Sanctum::actingAs($student);

        $create = $this->postJson('/api/v1/orders', [
            'course_id' => $course->id,
        ]);

        $create->assertCreated()->assertJsonPath('success', true);

        $order = Order::firstOrFail();

        $this->getJson("/api/v1/orders/{$order->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $order->id);

        Sanctum::actingAs($otherStudent);

        $this->getJson("/api/v1/orders/{$order->id}")
            ->assertNotFound();
    }
}
