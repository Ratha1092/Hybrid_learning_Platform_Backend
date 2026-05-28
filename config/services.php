<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // OAuth Configuration
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],
    'bakong' => [
        'merchant_name' => env('BAKONG_MERCHANT_NAME', env('APP_NAME', 'Hybrid Learning')),
        'merchant_city' => env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
        'merchant_account_id' => env('BAKONG_MERCHANT_ACCOUNT_ID', ''),
        'merchant_category_code' => env('BAKONG_MERCHANT_CATEGORY_CODE', '8299'),
        'country_code' => env('BAKONG_COUNTRY_CODE', 'KH'),
        'currency' => env('BAKONG_CURRENCY', 'USD'),
        'qr_ttl_minutes' => (int) env('BAKONG_QR_TTL_MINUTES', 15),
        'verify_url' => env('BAKONG_VERIFY_URL'),
        'api_token' => env('BAKONG_API_TOKEN'),
        'timeout' => (int) env('BAKONG_TIMEOUT', 10),
        'acquiring_bank' => env('BAKONG_ACQUIRING_BANK'),
        'merchant_id' => env('BAKONG_MERCHANT_ID'),
    ],

];
