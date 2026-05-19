<?php

return [
    'bakong' => [
        'merchant_name' => env('BAKONG_MERCHANT_NAME', env('APP_NAME', 'Hybrid Learning')),
        'merchant_city' => env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
        'merchant_account_id' => env('BAKONG_MERCHANT_ACCOUNT_ID'),
        'merchant_category_code' => env('BAKONG_MERCHANT_CATEGORY_CODE', '8299'),
        'country_code' => env('BAKONG_COUNTRY_CODE', 'KH'),
        'currency' => env('BAKONG_CURRENCY', 'USD'),
        'qr_ttl_minutes' => (int) env('BAKONG_QR_TTL_MINUTES', 15),
        'verify_url' => env('BAKONG_VERIFY_URL'),
        'api_token' => env('BAKONG_API_TOKEN'),
        'timeout' => (int) env('BAKONG_TIMEOUT', 10),
    ],
];
