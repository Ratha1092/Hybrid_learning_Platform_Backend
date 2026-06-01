<?php

namespace App\Domains\Payments\Services;

use App\Domains\Learning\Services\EnrollmentService;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Enums\PaymentGateway;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Payments\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Models\MerchantInfo;
use RuntimeException;

class BakongKhqrService
{
    public function __construct(
        private readonly BakongConfig $config
    ) {}

    public function createPaymentForOrder(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {

            $payment = Payment::query()->create([
                'order_id' => $order->id,
                'payment_gateway' => PaymentGateway::Bakong,
                'external_reference' => $this->referenceFor($order),
                'idempotency_key' => 'bakong:' . $order->order_number,
                'amount' => (float) $order->final_amount,
                'currency' => strtoupper($order->currency),
                'status' => PaymentStatus::Pending,
                'expires_at' => now()->addMinutes($this->config->qrTtlMinutes),
                'meta' => [
                    'order_number' => $order->order_number,
                ],
            ]);

            $payment->forceFill([
                'khqr_payload' => $this->generateKhqrPayload($payment),
            ])->save();

            $order->update([
                'payment_status' => OrderPaymentStatus::Pending,
                'payment_method' => PaymentGateway::Bakong->value,
            ]);

            return $payment->refresh();
        });
    }

    public function generateKhqrPayload(Payment $payment): string
    {
        $order = $payment->order;

        $accountId = trim((string) $this->config->merchantAccountId);

        if (empty($accountId)) {
            throw new RuntimeException('Bakong merchant account ID is missing.');
        }

        $currency = strtoupper((string) $payment->currency);
        $isKhr = $currency === 'KHR';
        $currencyCode = $isKhr ? 116 : 840;
        $rawAmount = (float) $payment->amount;

        if ($rawAmount <= 0) {
            throw new RuntimeException('Amount must be greater than zero.');
        }

        $amount = $isKhr
            ? (float) intval(round($rawAmount))
            : (float) number_format($rawAmount, 2, '.', '');
        $billNumber = mb_substr(
            (string) $order->order_number, 0, 25
        );

        $isMerchant = ! empty($this->config->merchantId) && ! empty($this->config->acquiringBank);

        if ($isMerchant) {
            $response = BakongKHQR::generateMerchant(new MerchantInfo(
                bakongAccountID: $accountId,
                merchantName: mb_substr((string) $this->config->merchantName, 0, 25),
                merchantCity: mb_substr((string) $this->config->merchantCity, 0, 15),
                merchantID: (string) $this->config->merchantId,
                acquiringBank: (string) $this->config->acquiringBank,
                currency: $currencyCode,
                amount: $amount,
                billNumber: $billNumber,
            ));
        } else {
            $response = BakongKHQR::generateIndividual(new IndividualInfo(
                bakongAccountID: $accountId,
                merchantName: mb_substr((string) $this->config->merchantName, 0, 25),
                merchantCity: mb_substr((string) $this->config->merchantCity, 0, 15),
                currency: $currencyCode,
                amount: $amount,
                billNumber: $billNumber,
            ));
        }

        return $response->data['qr'];
    }

    public function verifyPayment(Payment $payment): Payment
    {
        if ($payment->isPaid()) {
            return $payment->refresh();
        }

        if ($payment->isFailed() || $payment->isExpired()) {
            return $payment->refresh();
        }

        if (
            $payment->expires_at !== null &&
            $payment->expires_at->isPast()
        ) {
            return $this->expirePayment($payment);
        }

        $payment = $this->markVerificationStarted($payment);

        try {

            $gatewayResponse = $this->requestBakongVerification($payment);

        } catch (
            ConnectionException |
            RequestException $exception
        ) {

            return $this->markVerificationTemporarilyUnavailable(
                $payment,
                $exception
            );
        }

        $status = $this->extractVerificationStatus($gatewayResponse);

        if ($status === PaymentStatus::Paid) {
            return $this->markAsPaid($payment, $gatewayResponse);
        }

        if ($status === PaymentStatus::Failed) {
            return $this->markAsFailed($payment, $gatewayResponse);
        }

        $payment->transactions()->create([
            'gateway' => PaymentGateway::Bakong->value,
            'event_type' => 'payment.verify',
            'status' => $status->value,
            'payload' => $gatewayResponse,
        ]);

        $payment->update([
            'status' => PaymentStatus::Processing,
            'failure_reason' => null,
            'gateway_response' => $gatewayResponse,
        ]);

        return $payment->refresh();
    }

    public function expirePayment(Payment $payment): Payment
    {
        if ($payment->isPaid() || $payment->isExpired()) {
            return $payment->refresh();
        }

        $payment->update([
            'status' => PaymentStatus::Expired,
            'failure_reason' => 'KHQR payment expired before verification.',
        ]);

        $payment->order()->update([
            'payment_status' => OrderPaymentStatus::Expired,
        ]);

        return $payment->refresh();
    }

    public function markAsPaid(
        Payment $payment,
        array $gatewayResponse
    ): Payment {

        return DB::transaction(function () use (
            $payment,
            $gatewayResponse
        ) {

            $payment = Payment::query()
                ->lockForUpdate()
                ->with('order.items')
                ->findOrFail($payment->id);

            if ($payment->isPaid()) {
                return $payment->refresh();
            }

            $this->assertGatewayAmountMatches(
                $payment,
                $gatewayResponse
            );

            $payment->update([
                'status' => PaymentStatus::Paid,
                'transaction_id' => $this->extractTransactionId($gatewayResponse),
                'payer_account' =>
                    Arr::get($gatewayResponse, 'payer_account')
                    ?? Arr::get($gatewayResponse, 'data.payer_account'),
                'gateway_response' => $gatewayResponse,
                'paid_at' => now(),
                'failure_reason' => null,
            ]);

            $payment->transactions()->create([
                'gateway' => PaymentGateway::Bakong->value,
                'event_type' => 'payment.verified',
                'status' => PaymentStatus::Paid->value,
                'payload' => $gatewayResponse,
            ]);

            $payment->order->update([
                'status' => OrderStatus::Completed,
                'payment_status' => OrderPaymentStatus::Paid,
                'payment_method' => PaymentGateway::Bakong->value,
                'paid_at' => now(),
            ]);

            app(EnrollmentService::class)
                ->enrollFromOrder($payment->order);

            PaymentSuccessEvent::dispatch($payment->order);

            return $payment->refresh();
        });
    }

    private function markAsFailed(
        Payment $payment,
        array $gatewayResponse
    ): Payment {

        $payment->update([
            'status' => PaymentStatus::Failed,
            'failure_reason' => Arr::get(
                $gatewayResponse,
                'message',
                'Bakong verification failed.'
            ),
            'gateway_response' => $gatewayResponse,
        ]);

        $payment->transactions()->create([
            'gateway' => PaymentGateway::Bakong->value,
            'event_type' => 'payment.verify',
            'status' => PaymentStatus::Failed->value,
            'payload' => $gatewayResponse,
        ]);

        $payment->order()->update([
            'payment_status' => OrderPaymentStatus::Failed,
        ]);

        return $payment->refresh();
    }

    private function markVerificationStarted(
        Payment $payment
    ): Payment {

        $payment->forceFill([
            'status' => PaymentStatus::Processing,
            'verification_attempts' =>
                ((int) $payment->verification_attempts) + 1,
            'last_verified_at' => now(),
            'failure_reason' => null,
        ])->save();

        return $payment->refresh();
    }

    private function markVerificationTemporarilyUnavailable(
        Payment $payment,
        ConnectionException|RequestException $exception
    ): Payment {

        $payload = [
            'error' => class_basename($exception),
            'message' => $exception->getMessage(),
        ];

        if (
            $exception instanceof RequestException &&
            $exception->response !== null
        ) {
            $payload['status'] = $exception->response->status();
            $payload['body'] = $exception->response->json();
        }

        $payment->update([
            'status' => PaymentStatus::Processing,
            'failure_reason' =>
                'Temporary Bakong gateway issue. Please retry verification shortly.',
            'gateway_response' => $payload,
        ]);

        $payment->transactions()->create([
            'gateway' => PaymentGateway::Bakong->value,
            'event_type' => 'payment.verify_unavailable',
            'status' => PaymentStatus::Processing->value,
            'payload' => $payload,
        ]);

        return $payment->refresh();
    }

    private function requestBakongVerification(
        Payment $payment
    ): array {

        if (!$this->config->verifyUrl) {
            throw new RuntimeException(
                'BAKONG_VERIFY_URL is not configured.'
            );
        }

        $request = Http::timeout($this->config->timeout)
            ->acceptJson();

        if ($this->config->apiToken) {
            $request = $request->withToken(
                $this->config->apiToken
            );
        }

        return $request->post(
            $this->config->verifyUrl,
            [
                'external_reference' => $payment->external_reference,
                'transaction_id' => $payment->transaction_id,
                'amount' => $this->formatAmount($payment->amount),
                'currency' => $payment->currency,
            ]
        )->throw()->json();
    }

    private function extractVerificationStatus(
        array $response
    ): PaymentStatus {

        $rawStatus = strtolower((string) (
            Arr::get($response, 'status')
            ?? Arr::get($response, 'data.status')
            ?? Arr::get($response, 'payment_status')
            ?? ''
        ));

        $success =
            Arr::get($response, 'success')
            ?? Arr::get($response, 'data.success');

        $paid =
            Arr::get($response, 'paid')
            ?? Arr::get($response, 'data.paid');

        if (
            in_array(
                $rawStatus,
                ['paid', 'success', 'successful', 'completed'],
                true
            )
            || $success === true
            || $paid === true
        ) {
            return PaymentStatus::Paid;
        }

        if (
            in_array(
                $rawStatus,
                ['failed', 'failure', 'declined', 'cancelled', 'canceled'],
                true
            )
            || $success === false
        ) {
            return PaymentStatus::Failed;
        }

        return PaymentStatus::Processing;
    }

    private function assertGatewayAmountMatches(
        Payment $payment,
        array $response
    ): void {

        $amount =
            Arr::get($response, 'amount')
            ?? Arr::get($response, 'data.amount');

        $currency =
            Arr::get($response, 'currency')
            ?? Arr::get($response, 'data.currency');

        if (
            $amount !== null &&
            $this->formatAmount($amount)
            !== $this->formatAmount($payment->amount)
        ) {
            throw new RuntimeException(
                'Verified payment amount does not match the order amount.'
            );
        }

        if (
            $currency !== null &&
            strtoupper((string) $currency)
            !== strtoupper((string) $payment->currency)
        ) {
            throw new RuntimeException(
                'Verified payment currency does not match the order currency.'
            );
        }
    }

    private function extractTransactionId(
        array $response
    ): ?string {

        return
            Arr::get($response, 'transaction_id')
            ?? Arr::get($response, 'data.transaction_id')
            ?? Arr::get($response, 'hash')
            ?? Arr::get($response, 'data.hash');
    }

    private function referenceFor(Order $order): string
    {
        return 'KHQR-' . $order->order_number;
    }

    private function formatAmount($amount): string
    {
        return number_format(
            (float) $amount,
            2,
            '.',
            ''
        );
    }
}
