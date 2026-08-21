<?php

namespace App\Domains\Learning\Listeners;

use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Domains\Analytics\Jobs\RecordEnrollmentJob;


class EnrollStudentListener implements ShouldQueue
{
    public string $queue = 'high';
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

            // Enrollment creation itself is handled synchronously by
            // EnrollmentService::enrollFromOrder (called from the payment
            // success path before this event fires) — it already covers
            // expires_at, trashed-row restore, and expired-row renewal.
            // Duplicating that here as a separate queued write raced with
            // it and could create a second, incomplete enrollment record.

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