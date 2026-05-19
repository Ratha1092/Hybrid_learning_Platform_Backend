<?php

namespace App\Domains\Orders\Services;

use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Payments\Services\BakongKhqrService;
use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly BakongKhqrService $bakongKhqrService
    ) {
    }

    public function createOrder(User $user, int $courseId): Order
    {
        return DB::transaction(function () use ($user, $courseId) {

            $course = Course::where('id', $courseId)
                ->where('is_published', true)
                ->firstOrFail();

            if ((int) $course->instructor_id === (int) $user->id) {
                throw new \RuntimeException('You cannot purchase your own course');
            }

            if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                throw new \RuntimeException('You are already enrolled in this course');
            }

            // 🔥 Generate order number
            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . uniqid();

            // Create Order
            $order = Order::create([
                'order_number' => $orderNumber, // ✅ FIX
                'user_id' => $user->id,
                'total_amount' => $course->price,
                'discount_amount' => 0,
                'final_amount' => $course->price,
                'currency' => config('payments.bakong.currency', 'USD'),
                'status' => OrderStatus::Pending,
                'payment_status' => OrderPaymentStatus::Pending,
                'customer_name' => $user->name ?? 'Guest',
                'customer_email' => $user->email ?? null,
            ]);
            $commissionPercentage = 20;
            $platformAmount = ((float) $course->price * $commissionPercentage) / 100;
            $instructorAmount = (float) $course->price - $platformAmount;

            // Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $course->price,
                'discount_amount' => 0,
                'final_amount' => $course->price,
                'instructor_id' => $course->instructor_id,
                'course_title' => $course->title,
                'commission_percentage' => $commissionPercentage,
                'instructor_amount' => $instructorAmount,
                'platform_amount' => $platformAmount,
            ]);

            $this->bakongKhqrService->createPaymentForOrder($order);

            return $order;
        });
    }
}
