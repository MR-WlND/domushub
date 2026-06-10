<?php

return [
    'mbbank' => [
        'account_number' => env('MBBANK_ACCOUNT_NUMBER', ''),
        'account_name'   => env('MBBANK_ACCOUNT_NAME', ''),
    ],

    'momo' => [
        'phone' => env('MOMO_PHONE', ''),
        'name'  => env('MOMO_NAME', ''),
    ],

    'sepay' => [
        'webhook_secret' => env('SEPAY_WEBHOOK_SECRET', ''),
        'account_number' => env('MBBANK_ACCOUNT_NUMBER', ''),
        'account_name'   => env('MBBANK_ACCOUNT_NAME', ''),
        'bank'           => 'MB',
    ],

    'callback_timeout' => env('PAYMENT_CALLBACK_TIMEOUT', 3600),
];
