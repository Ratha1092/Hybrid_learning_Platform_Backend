<?php

namespace App\Domains\Finance\Listeners;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\System\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domains\Analytics\Jobs\RecordRevenueJob;

class ProcessRevenueListener implements ShouldQueue
{
    public string $queue = 'high';
    public $tries = 3;

    public function tags(): array
    {
        return ['finance', 'revenue'];
    }

    public function handle(PaymentSuccessEvent $event): void
    {
        $orderId = $event->order?->id;
        $lock    = Cache::lock("process_revenue_{$orderId}", 300);
        if (! $lock->get()) {
            return;
        }

        try {
            $order = $event->order;

            if (!$order || $order->items->isEmpty()) {
                Log::warning('ProcessRevenueListener: Invalid order data', [
                    'order_id' => $order?->id
                ]);
                return;
            }

            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $share = RevenueShare::updateOrCreate(
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

                    $wallet = InstructorWallet::firstOrCreate(
                        ['instructor_id' => $share->instructor_id],
                        [
                            'balance' => 0,
                            'pending_balance' => 0,
                            'currency' => Setting::get('default_currency', 'USD'),
                        ]
                    );

                    // Only credit a wallet once. Retried payment jobs reuse the
                    // same revenue share and therefore cannot double-credit it.
                    $transaction = WalletTransaction::firstOrCreate(
                        ['revenue_share_id' => $share->id, 'type' => 'credit'],
                        [
                            'instructor_id' => $share->instructor_id,
                            'amount' => $share->instructor_amount,
                            'status' => 'completed',
                            'description' => 'Course sale revenue',
                        ]
                    );

                    if ($transaction->wasRecentlyCreated) {
                        $wallet->increment('pending_balance', $share->instructor_amount);
                    }
                }
            });

            dispatch(new RecordRevenueJob($order->final_amount));
            ActivityLogService::logChange('payment.completed', $order);

            foreach ($order->items as $item) {
                Cache::forget("instructor:{$item->instructor_id}:dashboard");
                Cache::forget("finance:{$item->instructor_id}:earnings");
            }

            Log::info('Revenue processed successfully', [
                'order_id' => $order->id
            ]);

        } catch (\Throwable $e) {
            Log::error('ProcessRevenueListener failed', [
                'error'    => $e->getMessage(),
                'order_id' => $event->order?->id,
            ]);

            throw $e; // important for retry
        } finally {
            $lock->release();
        }
    }
}
