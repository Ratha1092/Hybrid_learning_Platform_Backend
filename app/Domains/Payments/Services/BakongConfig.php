<?php

namespace App\Domains\Payments\Services;

class BakongConfig
{
    public function __construct(
        public readonly string $merchantName,
        public readonly string $merchantCity,
        public readonly string $merchantAccountId,
        public readonly string $merchantCategoryCode,
        public readonly string $countryCode,
        public readonly string $currency,
        public readonly int $qrTtlMinutes,
        public readonly ?string $verifyUrl,
        public readonly ?string $apiToken,
        public readonly int $timeout,
        public readonly ?string $acquiringBank,
        public readonly ?string $merchantId,
    ) {}
}
