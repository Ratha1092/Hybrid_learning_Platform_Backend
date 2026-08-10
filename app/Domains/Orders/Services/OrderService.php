<?php

namespace App\Domains\Orders\Services;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Payments\Services\BakongConfig;
use App\Domains\Payments\Services\BakongKhqrService;
use App\Domains\Learning\Services\EnrollmentService;
use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\AdminNewOrderNotification;
use App\Jobs\Notifications\NotifyAdminsJob;
use App\Domains\Promotions\Models\Coupon;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly BakongKhqrService $bakongKhqrService,
        private readonly BakongConfig $bakongConfig,
    ) {}


    public function createOrder(User $user, int $courseId, ?string $couponCode = null): Order
    {
        $order = DB::transaction(function () use ($user, $courseId, $couponCode) {
            $course = Course::where('id', $courseId)
                ->where('is_published', true)
                ->firstOrFail();

            if ((int) $course->instructor_id === (int) $user->id) {
                throw new \RuntimeException('You cannot purchase your own course');
            }

            if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                throw new \RuntimeException('You are already enrolled in this course');
            }

            $coupon = null;
            $discountAmount = 0.0;

            if ($couponCode && (float) $course->price > 0) {
                $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();

                if (!$coupon) {
                    throw new \RuntimeException('Invalid coupon code');
                }

                if ($error = $coupon->validateFor($user, (float) $course->price)) {
                    throw new \RuntimeException($error);
                }

                $discountAmount = $coupon->calculateDiscount((float) $course->price);
            }

            $finalAmount = round((float) $course->price - $discountAmount, 2);

            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . uniqid();
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'total_amount' => $course->price,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'currency' => $this->bakongConfig->currency,
                'status' => OrderStatus::Pending,
                'payment_status' => OrderPaymentStatus::Pending,
                'customer_name' => $user->name ?? 'Guest',
                'customer_email' => $user->email ?? null,
                'coupon_code' => $coupon?->code,
                'coupon_id' => $coupon?->id,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $commissionPercentage = 20;
            $platformAmount = ($finalAmount * $commissionPercentage) / 100;
            $instructorAmount = $finalAmount - $platformAmount;
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $course->price,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'instructor_id' => $course->instructor_id,
                'course_title' => $course->title,
                'commission_percentage' => $commissionPercentage,
                'instructor_amount' => $instructorAmount,
                'platform_amount' => $platformAmount,
            ]);

            if ((float) $course->price === 0.0) {
                $order->update([
                    'status' => OrderStatus::Completed,
                    'payment_status' => OrderPaymentStatus::Paid,
                    'paid_at' => now(),
                ]);
                app(EnrollmentService::class)->enrollFromOrder($order);
                return $order;
            }

            if (!Setting::get('bakong_enabled', true) && !Setting::get('khqr_enabled', true)) {
                throw new \RuntimeException('Online payment is currently unavailable. Please try again later.');
            }

            $this->bakongKhqrService->createPaymentForOrder($order);

            return $order;
        });
        ActivityLogService::logChange('order.placed', $order, [], [], $user);

        NotifyAdminsJob::dispatch(
            AdminNewOrderNotification::class,
            [$order->id, $user->name],
            'orders.view'
        );
        return $order;
    }
}
