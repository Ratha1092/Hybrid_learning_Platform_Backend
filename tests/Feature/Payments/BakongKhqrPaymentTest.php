<?php

namespace Tests\Feature\Payments;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BakongKhqrPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_bakong_payment_with_khqr_payload(): void
    {
        $student = User::factory()->create();
        $course = $this->createPublishedCourse();

        Sanctum::actingAs($student);

        $response = $this->postJson('/api/v1/checkout', [
            'course_id' => $course->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.payment.payment_gateway', 'bakong')
            ->assertJsonPath('data.payment.status', 'pending');

        $this->assertNotEmpty($response->json('data.payment.khqr_payload'));
        $this->assertStringContainsString('KHQR-', $response->json('data.payment.external_reference'));
    }

    public function test_verify_marks_payment_paid_from_backend_bakong_response(): void
    {
        config(['payments.bakong.verify_url' => 'https://bakong.test/verify']);

        $student = User::factory()->create();
        $course = $this->createPublishedCourse(price: 30);

        Sanctum::actingAs($student);

        $this->postJson('/api/v1/checkout', [
            'course_id' => $course->id,
        ])->assertCreated();

        $payment = Payment::firstOrFail();

        Http::fake([
            'https://bakong.test/verify' => Http::response([
                'status' => 'paid',
                'transaction_id' => 'txn_123',
                'amount' => '30.00',
                'currency' => 'USD',
                'payer_account' => 'student@bank',
            ]),
        ]);

        Event::fake([PaymentSuccessEvent::class]);

        $response = $this->postJson('/api/v1/payments/verify', [
            'payment_id' => $payment->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.verification_attempts', 1)
            ->assertJsonPath('data.transaction_id', 'txn_123');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
            'verification_attempts' => 1,
            'transaction_id' => 'txn_123',
            'payer_account' => 'student@bank',
        ]);
        $this->assertNotNull($payment->fresh()->last_verified_at);

        $this->assertDatabaseHas('orders', [
            'id' => $payment->order_id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'bakong',
        ]);

        Event::assertDispatched(PaymentSuccessEvent::class);
    }

    public function test_verify_keeps_payment_processing_when_bakong_is_temporarily_unavailable(): void
    {
        config(['payments.bakong.verify_url' => 'https://bakong.test/verify']);

        $student = User::factory()->create();
        $course = $this->createPublishedCourse(price: 30);

        Sanctum::actingAs($student);

        $this->postJson('/api/v1/checkout', [
            'course_id' => $course->id,
        ])->assertCreated();

        $payment = Payment::firstOrFail();

        Http::fake([
            'https://bakong.test/verify' => Http::response([
                'message' => 'Gateway timeout',
            ], 503),
        ]);

        $response = $this->postJson('/api/v1/payments/verify', [
            'payment_id' => $payment->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.verification_attempts', 1);

        $payment->refresh();

        $this->assertSame('processing', $payment->status->value);
        $this->assertSame(1, $payment->verification_attempts);
        $this->assertNotNull($payment->last_verified_at);
        $this->assertSame('Temporary Bakong gateway issue. Please retry verification shortly.', $payment->failure_reason);

        $this->assertDatabaseHas('transactions', [
            'payment_id' => $payment->id,
            'event_type' => 'payment.verify_unavailable',
            'status' => 'processing',
        ]);
    }

    private function createPublishedCourse(int $price = 30): Course
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'verified',
        ]);

        $category = Category::create([
            'name' => 'Payments',
            'slug' => 'payments',
        ]);

        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'KHQR Checkout',
            'slug' => 'khqr-checkout',
            'price' => $price,
            'status' => 'published',
            'is_published' => true,
        ]);
    }
}
