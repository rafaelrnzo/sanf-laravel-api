<?php

return [
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_sanitized' => (bool) env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => (bool) env('MIDTRANS_IS_3DS', true),
    'snap' => [
        'expiry' => [
            'unit' => env('MIDTRANS_EXPIRY_UNIT', 'minutes'),
            'duration' => (int) env('MIDTRANS_EXPIRY_DURATION', 30),
        ],
        'finish_redirect_url' => env('MIDTRANS_FINISH_REDIRECT_URL'),
    ],
];
