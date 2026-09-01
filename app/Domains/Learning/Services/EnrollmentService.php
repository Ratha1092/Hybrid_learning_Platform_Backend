<?php

namespace App\Domains\Learning\Services;

use App\Domains\Learning\Models\Enrollment;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Promotions\Models\Coupon;
use App\Domains\System\Models\Setting;

use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function enrollFromOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {

            $order->loadMissing('items');
            foreach ($order->items as $item) {
                $this->createEnrollment(
                    $order,
                    $item
                );
            }
            if ($order->coupon_id) {
                Coupon::where('id', $order->coupon_id)->lockForUpdate()->increment('used_count');
            }
        });
    }
    protected function createEnrollment(
        Order $order,
        OrderItem $item
    ): void {
        $isFreeCourse   = (float) $item->price <= 0;
        $durationMonths = $isFreeCourse ? 0 : (int) Setting::get('course_access_duration_months', 6);

        $existing = Enrollment::withTrashed()
            ->where('user_id', $order->user_id)
            ->where('course_id', $item->course_id)
            ->first();

        if ($existing) {
            $wasTrashed = $existing->trashed();

            if ($wasTrashed) {
                $existing->restore();
            }

            if ($wasTrashed || $existing->isExpired()) {
                $existing->update([
                    'order_id' => $order->id,
                    'source' => 'purchase',
                    'status' => 'active',
                    'progress_percentage' => 0,
                    'enrolled_at' => now(),
                    'expires_at' => $durationMonths > 0 ? now()->addMonths($durationMonths) : null,
                ]);
            }

            return;
        }

        Enrollment::create([
            'user_id' => $order->user_id,
            'course_id' => $item->course_id,
            'order_id' => $order->id,
            'source' => 'purchase',
            'status' => 'active',
            'progress_percentage' => 0,
            'enrolled_at' => now(),
            'expires_at' => $durationMonths > 0 ? now()->addMonths($durationMonths) : null,
        ]);
    }
}