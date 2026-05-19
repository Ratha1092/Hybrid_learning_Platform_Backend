<?php

namespace App\Domains\Finance\Listeners;

use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Domains\Analytics\Jobs\RecordRevenueJob;

class ProcessRevenueListener implements ShouldQueue
{
    public $tries = 3;

    public function handle(PaymentSuccessEvent $event): void
    {
        try {
            $order = $event->order;

            if (!$order || $order->items->isEmpty()) {
                Log::warning('ProcessRevenueListener: Invalid order data', [
                    'order_id' => $order?->id
                ]);
                return;
            }

            foreach ($order->items as $item) {

                RevenueShare::updateOrCreate(
                    ['order_item_id' => $item->id],
                    [
                        'instructor_id' => $item->instructor_id,
                        'total_amount' => $item->price,
                        'platform_amount' => ($item->price * $item->commission_percentage) / 100,
                        'instructor_amount' => $item->price * (1 - $item->commission_percentage / 100),
                        'commission_percentage' => $item->commission_percentage,
                        'status' => 'pending',
                    ]
                );
            }

            // ✅ NEW: Analytics tracking
            dispatch(new RecordRevenueJob($order->final_amount));

            Log::info('Revenue processed successfully', [
                'order_id' => $order->id
            ]);

        } catch (\Throwable $e) {
            Log::error('ProcessRevenueListener failed', [
                'error' => $e->getMessage(),
                'order_id' => $event->order?->id
            ]);

            throw $e; // important for retry
        }
    }
}