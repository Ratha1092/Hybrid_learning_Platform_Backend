<?php

namespace App\Domains\Finance\Listeners;

use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateInstructorWalletListener implements ShouldQueue
{
    public $tries = 3;

    public function handle(PaymentSuccessEvent $event): void
    {
        $shares = RevenueShare::whereIn(
            'order_item_id',
            $event->order->items->pluck('id')
        )->get();

        foreach ($shares as $share) {
            if (WalletTransaction::where('revenue_share_id', $share->id)->exists()) {
                continue;
            }

            $wallet = InstructorWallet::firstOrCreate(
                ['instructor_id' => $share->instructor_id],
                ['balance' => 0, 'pending_balance' => 0]
            );

            $wallet->increment('pending_balance', $share->instructor_amount);
        }

        Log::info('Processing revenue', [
            'order_id' => $event->order->id
        ]);
    }
}