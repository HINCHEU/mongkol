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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    // Admin alerts (new payment proofs). The chat id can also be set in admin settings (telegram_chat_id).
    // Automatic payment (Phase 2). Without a token and account id the Upgrade page stays manual.
    // The Open API token expires every 90 days: renew it at https://api-bakong.nbc.gov.kh/register.
    'bakong' => [
        'token' => env('BAKONG_TOKEN'),
        'account_id' => env('BAKONG_ACCOUNT_ID'), // e.g. yourname@aclb
        'merchant_name' => env('BAKONG_MERCHANT_NAME', env('APP_NAME')),
        'merchant_city' => env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
        'url' => env('BAKONG_URL', 'https://api-bakong.nbc.gov.kh'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
