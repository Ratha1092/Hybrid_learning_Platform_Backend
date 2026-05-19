<?php

namespace App\Domains\Learning\Listeners;

use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Domains\Analytics\Jobs\RecordEnrollmentJob;


class EnrollStudentListener implements ShouldQueue
{
    public $tries = 3;

    public function handle(PaymentSuccessEvent $event): void
    {
        try {
            $order = $event->order;

            if (!$order || $order->items->isEmpty()) {
                Log::warning('EnrollStudentListener: Invalid order data', [
                    'order_id' => $order?->id
                ]);
                return;
            }

            foreach ($order->items as $item) {

                Enrollment::updateOrCreate(
                    [
                        'user_id' => $order->user_id,
                        'course_id' => $item->course_id,
                    ],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'source' => 'purchase',
                        'enrolled_at' => now(),
                    ]
                );
            }

            // ✅ NEW: Analytics tracking
            dispatch(new RecordEnrollmentJob());

            Log::info('Student enrolled successfully', [
                'order_id' => $order->id
            ]);

        } catch (\Throwable $e) {
            Log::error('EnrollStudentListener failed', [
                'error' => $e->getMessage(),
                'order_id' => $event->order?->id
            ]);

            throw $e;
        }
    }
}