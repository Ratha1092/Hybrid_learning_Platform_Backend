<?php

namespace App\Domains\Finance\Listeners;

use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Finance\Models\RevenueShare;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class RecordWalletTransactionListener implements ShouldQueue
{
    public $tries = 3;

    public function handle(PaymentSuccessEvent $event): void
    {
        $shares = RevenueShare::whereIn(
            'order_item_id',
            $event->order->items->pluck('id')
        )->get();

        foreach ($shares as $share) {
            WalletTransaction::firstOrCreate(
                [
                    'revenue_share_id' => $share->id,
                    'type' => 'credit',
                ],
                [
                    'instructor_id' => $share->instructor_id,
                    'amount' => $share->instructor_amount,
                    'status' => 'completed',
                    'description' => 'Course sale revenue',
                ]
            );
        }
        Log::info('Processing revenue', [
            'order_id' => $event->order->id
        ]);
    }
}