<?php

namespace App\Domains\Payments\Controllers;

use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\BakongKhqrService;
use App\Domains\Payments\Services\QrCodeService;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use RuntimeException;

class PaymentController extends Controller
{
    public function __construct(
        private readonly BakongKhqrService $bakongKhqrService,
        private readonly QrCodeService $qrCodeService
    ) {}

    public function status(Request $request, Payment $payment)
    {
        if ((int) $payment->order->user_id !== (int) $request->user()->id) {
            return ApiResponse::error('Payment not found', 404);
        }

        if ($payment->isPending() && $payment->expires_at !== null && $payment->expires_at->isPast()) {
            $payment = $this->bakongKhqrService->expirePayment($payment);
        }

        return ApiResponse::success($this->paymentStatusPayload($payment), 'Payment status retrieved successfully');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'integer', 'exists:payments,id'],
        ]);

        $payment = Payment::with('order')->findOrFail($validated['payment_id']);

        if ((int) $payment->order->user_id !== (int) $request->user()->id) {
            return ApiResponse::error('Payment not found', 404);
        }

        try {
            $payment = $this->bakongKhqrService->verifyPayment($payment);
        } catch (RequestException $exception) {
            return ApiResponse::error('Bakong verification failed', 502, [
                'detail' => $exception->getMessage(),
            ]);
        } catch (RuntimeException $exception) {
            return ApiResponse::error($exception->getMessage(), 422);
        }

        return ApiResponse::success($this->paymentStatusPayload($payment), 'Payment verified successfully');
    }

    private function paymentStatusPayload(Payment $payment): array
    {
        $payment->loadMissing('order');

        $qrCodeImage = null;
        if ($payment->khqr_payload && $payment->isPending()) {
            try {
                $qrCodeImage = $this->qrCodeService->generateQrCodeFromPayload($payment->khqr_payload);
            } catch (\Exception) {
                // QR generation failed, continue without it
            }
        }

        return [
            'id' => $payment->id,
            'order_id' => $payment->order_id,
            'order_number' => $payment->order?->order_number,
            'payment_gateway' => $payment->payment_gateway,
            'status' => $payment->status,
            'verification_attempts' => $payment->verification_attempts,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'khqr_payload' => $payment->khqr_payload,
            'qr_code_image' => $qrCodeImage,
            'external_reference' => $payment->external_reference,
            'transaction_id' => $payment->transaction_id,
            'paid_at' => $payment->paid_at,
            'last_verified_at' => $payment->last_verified_at,
            'expires_at' => $payment->expires_at,
            'expires_in_seconds' => $payment->expires_at ? max(now()->diffInSeconds($payment->expires_at, false), 0) : null,
        ];
    }
}
