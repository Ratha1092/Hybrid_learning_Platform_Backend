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
use KHQR\Helpers\Utils;
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
            $expiresAt = $payment->expires_at ?? now()->addMinutes($this->config->qrTtlMinutes);
            $creationTimestampMs = now()->getTimestampMs();
            $expirationTimestampMs = $expiresAt->getTimestampMs();
            $merchantCategoryCode = $this->normalizeMerchantCategoryCode();
            $billNumber = mb_substr(
                (string) $order->order_number, 0, 25
            );

            $isMerchant = ! empty($this->config->merchantId) && ! empty($this->config->acquiringBank);

            if ($isMerchant) {
                $response = BakongKHQR::generateMerchant(new MerchantInfo(
                    bakongAccountID: $accountId,
                    merchantName: mb_substr((string) $this->config->merchantName, 0, 25),
                    merchantCity: mb_strtoupper(mb_substr((string) $this->config->merchantCity, 0, 15)),
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
                    merchantCity: mb_strtoupper(mb_substr((string) $this->config->merchantCity, 0, 15)),
                    currency: $currencyCode,
                    amount: $amount,
                    billNumber: $billNumber,
                ));
            }

            $payload = $response->data['qr'] ?? null;

            if (! is_string($payload) || $payload === '') {
                throw new RuntimeException('Bakong KHQR generation failed.');
            }

            $payload = $this->normalizeKhqrPayload(
                $payload,
                $merchantCategoryCode,
                $creationTimestampMs,
                $expirationTimestampMs
            );

            $this->assertGeneratedKhqrIsValid(
                $payload,
                $accountId,
                $currencyCode,
                $amount,
                $billNumber,
                $merchantCategoryCode,
                $creationTimestampMs,
                $expirationTimestampMs
            );

            return $payload;
        }

        public function refreshKhqrPayload(Payment $payment): Payment
        {
            if (! $payment->isPending() || $payment->hasExpired()) {
                return $payment->refresh();
            }

            $payment->loadMissing('order');

            $payload = $this->generateKhqrPayload($payment);

            if ($payment->khqr_payload !== $payload) {
                $payment->forceFill([
                    'khqr_payload' => $payload,
                ])->save();
            }

            return $payment->refresh();
        }

        public function forceVerifyPayment(Payment $payment): Payment
        {
            if ($payment->isPaid()) {
                return $payment->refresh();
            }

            if ($payment->isFailed() || $payment->isExpired()) {
                return $payment->refresh();
            }

            if ($payment->expires_at !== null && $payment->expires_at->isPast()) {
                return $this->expirePayment($payment);
            }

            $payment = $this->markVerificationStarted($payment);

            try {
                $gatewayResponse = $this->requestBakongVerification($payment);
            } catch (
                ConnectionException |
                RequestException $exception
            ) {
                return $this->markVerificationTemporarilyUnavailable($payment, $exception);
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
                'event_type' => 'payment.force_verify',
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
                'status'         => OrderStatus::Cancelled,
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
                        ?? Arr::get($gatewayResponse, 'data.payer_account')
                        ?? Arr::get($gatewayResponse, 'data.fromAccountId')
                        ?? Arr::get($gatewayResponse, 'fromAccountId'),
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
                    'md5' => md5((string) $payment->khqr_payload),
                ]
            )->throw()->json();
        }

        private function extractVerificationStatus(
            array $response
        ): PaymentStatus {

            $responseCode = Arr::get($response, 'responseCode');
            $errorCode    = Arr::get($response, 'errorCode');

            // responseCode 0 = transaction found and succeeded
            if ($responseCode === 0) {
                return PaymentStatus::Paid;
            }

            // errorCode 3 = "Transaction failed" (definitive failure)
            if ($errorCode === 3) {
                return PaymentStatus::Failed;
            }

            // errorCode 1 = not found yet, errorCode 1 = still pending
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
            return (string) $order->order_number;
        }

        private function normalizeKhqrPayload(
            string $payload,
            string $merchantCategoryCode,
            int $creationTimestampMs,
            int $expirationTimestampMs
        ): string {

            $tags = $this->parseKhqrTags($payload);
            $normalizedTimestamp = $this->formatKhqrTag(
                '00',
                (string) $creationTimestampMs
            ) . $this->formatKhqrTag(
                '01',
                (string) $expirationTimestampMs
            );
            $changed = false;
            $hasTimestamp = false;

            foreach ($tags as &$tag) {
                if ($tag['tag'] === '52' && $tag['value'] !== $merchantCategoryCode) {
                    $tag['value'] = $merchantCategoryCode;
                    $changed = true;
                    continue;
                }

                if ($tag['tag'] === '99') {
                    $hasTimestamp = true;

                    if ($tag['value'] !== $normalizedTimestamp) {
                        $tag['value'] = $normalizedTimestamp;
                        $changed = true;
                    }
                }
            }

            unset($tag);

            if (! $hasTimestamp) {
                array_splice($tags, -1, 0, [[
                    'tag' => '99',
                    'value' => $normalizedTimestamp,
                ]]);
                $changed = true;
            }

            return $changed ? $this->buildKhqrPayload($tags) : $payload;
        }

        /**
         * @return array<int, array{tag: string, value: string}>
         */
        private function parseKhqrTags(string $payload): array
        {
            $tags = [];
            $remaining = $payload;

            while ($remaining !== '') {
                if (strlen($remaining) < 4) {
                    throw new RuntimeException('Generated KHQR payload is malformed.');
                }

                $tag = substr($remaining, 0, 2);
                $length = (int) substr($remaining, 2, 2);
                $value = substr($remaining, 4, $length);

                if (strlen($value) !== $length) {
                    throw new RuntimeException('Generated KHQR payload has an invalid tag length.');
                }

                $tags[] = [
                    'tag' => $tag,
                    'value' => $value,
                ];

                $remaining = substr($remaining, 4 + $length);
            }

            return $tags;
        }

        /**
         * @param array<int, array{tag: string, value: string}> $tags
         */
        private function buildKhqrPayload(array $tags): string
        {
            $payload = '';

            foreach ($tags as $tag) {
                if ($tag['tag'] === '63') {
                    continue;
                }

                $payload .= $this->formatKhqrTag($tag['tag'], $tag['value']);
            }

            $payload .= '6304';

            return $payload . Utils::crc16($payload);
        }

        private function formatKhqrTag(string $tag, string $value): string
        {
            return $tag
                . str_pad((string) mb_strlen($value, 'UTF-8'), 2, '0', STR_PAD_LEFT)
                . $value;
        }

        private function assertGeneratedKhqrIsValid(
            string $payload,
            string $accountId,
            int $currencyCode,
            float $amount,
            string $billNumber,
            string $merchantCategoryCode,
            int $creationTimestampMs,
            int $expirationTimestampMs
        ): void {

            if (! BakongKHQR::verify($payload)->isValid) {
                throw new RuntimeException('Generated KHQR payload failed checksum validation.');
            }

            $decoded = BakongKHQR::decode($payload)->data;

            if (($decoded['bakongAccountID'] ?? null) !== $accountId) {
                throw new RuntimeException('Generated KHQR account ID does not match Bakong configuration.');
            }

            if ((string) ($decoded['transactionCurrency'] ?? '') !== (string) $currencyCode) {
                throw new RuntimeException('Generated KHQR currency does not match the payment currency.');
            }
            $expectedAmount = $currencyCode === 116
                ? (string) intval(round($amount))
                : (floor($amount) == $amount
                    ? (string) intval($amount)
                    : number_format($amount, 2, '.', ''));

            if ((string) ($decoded['transactionAmount'] ?? '') !== $expectedAmount) {
                throw new RuntimeException('Generated KHQR amount does not match the payment amount.');
            }

            if ((string) ($decoded['merchantCategoryCode'] ?? '') !== $merchantCategoryCode) {
                throw new RuntimeException('Generated KHQR merchant category code does not match Bakong configuration.');
            }

            if (($decoded['billNumber'] ?? null) !== $billNumber) {
                throw new RuntimeException('Generated KHQR bill number does not match the order.');
            }

            if ((string) ($decoded['timestamp'] ?? '') !== '0013' . $creationTimestampMs . '0113' . $expirationTimestampMs) {
                throw new RuntimeException('Generated KHQR expiration timestamp does not match the payment expiry.');
            }
        }

        private function normalizeMerchantCategoryCode(): string
        {
            $merchantCategoryCode = trim((string) $this->config->merchantCategoryCode);

            if (! preg_match('/^\d{4}$/', $merchantCategoryCode)) {
                throw new RuntimeException('Bakong merchant category code must be exactly 4 digits.');
            }

            return $merchantCategoryCode;
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
