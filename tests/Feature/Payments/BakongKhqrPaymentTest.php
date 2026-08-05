<?php

namespace Tests\Feature\Payments;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use KHQR\BakongKHQR;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BakongKhqrPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.bakong.merchant_account_id' => 'studentstore@nbcq',
            'services.bakong.merchant_name' => 'Hybrid Learning',
            'services.bakong.merchant_city' => 'PHNOM PENH',
        ]);
    }

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

        $khqrPayload = $response->json('data.payment.khqr_payload');
        $decodedKhqr = BakongKHQR::decode($khqrPayload)->data;

        $this->assertNotEmpty($khqrPayload);
        $this->assertTrue(
            BakongKHQR::verify($khqrPayload)->isValid
        );
        $this->assertSame(
            'studentstore@nbcq',
            $decodedKhqr['bakongAccountID']
        );
        $this->assertSame(
            '30.00',
            number_format((float) $decodedKhqr['transactionAmount'], 2, '.', '')
        );
        // external_reference is the order_number (BakongKhqrService::referenceFor()),
        // e.g. "ORD-20260722204733-...", not a "KHQR-" prefixed value.
        $this->assertStringContainsString('ORD-', $response->json('data.payment.external_reference'));
    }

    public function test_verify_marks_payment_paid_from_backend_bakong_response(): void
    {
        config(['services.bakong.verify_url' => 'https://bakong.test/verify']);

        $student = User::factory()->create();
        $course = $this->createPublishedCourse(price: 30);

        Sanctum::actingAs($student);

        $this->postJson('/api/v1/checkout', [
            'course_id' => $course->id,
        ])->assertCreated();

        $payment = Payment::firstOrFail();

        // Real Bakong "check transaction by MD5" contract: responseCode 0 = paid
        // (see BakongKhqrService::extractVerificationStatus()), not {"status":"paid"}.
        Http::fake([
            'https://bakong.test/verify' => Http::response([
                'responseCode' => 0,
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

    public function test_successful_verification_enrolls_the_student_in_the_course(): void
    {
        // End-to-end business-logic check for the checkout -> verify -> enrollment
        // pipeline: unlike test_verify_marks_payment_paid_from_backend_bakong_response
        // above (which fakes PaymentSuccessEvent to isolate the payment-verify step),
        // this test lets the event actually dispatch. QUEUE_CONNECTION=sync in
        // phpunit.xml means EnrollStudentListener (ShouldQueue) runs inline, so we can
        // assert the real side effect: an active Enrollment row for the student/course.
        config(['services.bakong.verify_url' => 'https://bakong.test/verify']);

        $student = User::factory()->create();
        $course = $this->createPublishedCourse(price: 30);

        Sanctum::actingAs($student);

        $this->postJson('/api/v1/checkout', [
            'course_id' => $course->id,
        ])->assertCreated();

        $payment = Payment::firstOrFail();

        // Shape matches what BakongKhqrService::extractVerificationStatus /
        // markAsPaid actually read (responseCode 0 = paid; transaction_id,
        // amount, currency, payer_account) -- NOT the {"status":"paid",...}
        // shape used by the other tests in this file, which predates a
        // refactor of the service and currently fails against the real
        // Bakong "check transaction by MD5" response contract.
        Http::fake([
            'https://bakong.test/verify' => Http::response([
                'responseCode' => 0,
                'transaction_id' => 'txn_enroll_1',
                'amount' => '30.00',
                'currency' => 'USD',
                'payer_account' => 'student@bank',
            ]),
        ]);

        $this->postJson('/api/v1/payments/verify', [
            'payment_id' => $payment->id,
        ])->assertOk()->assertJsonPath('data.status', 'paid');

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'order_id' => $payment->order_id,
            'status' => 'active',
            'source' => 'purchase',
        ]);

        $enrollment = Enrollment::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $this->assertNotNull($enrollment->enrolled_at);
    }

    public function test_verify_keeps_payment_processing_when_bakong_is_temporarily_unavailable(): void
    {
        config(['services.bakong.verify_url' => 'https://bakong.test/verify']);

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
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        \App\Domains\Users\Models\InstructorVerification::create([
            'user_id' => $instructor->id,
            'status' => 'approved',
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
