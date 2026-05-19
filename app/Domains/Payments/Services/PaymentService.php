<?php

namespace App\Domains\Payments\Services;

use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Payments\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function handleSuccessfulPayment($paymentId)
    {
        return DB::transaction(function () use ($paymentId) {

            $payment = Payment::findOrFail($paymentId);

            if ($payment->isPaid()) {
                return $payment;
            }

            $payment->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => now()
            ]);

            $order = $payment->order;

            $order->update([
                'status' => OrderStatus::Completed,
                'payment_status' => OrderPaymentStatus::Paid,
                'paid_at' => now(),
            ]);

            PaymentSuccessEvent::dispatch($order);

            return $payment;
        });
    }
}
