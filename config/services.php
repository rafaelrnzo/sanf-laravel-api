<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'sanf' => [
        'base_url_mobile' => env('SANF_BASE_URL_MOBILE', env('SANF_INTERNAL_URL')),
        'base_url_core' => env('SANF_BASE_URL_CORE', env('SANF_INTERNAL_V2_URL')),
        'timeout' => (int) env('SANF_TIMEOUT', 30),
        'auth_token' => env('SANF_AUTH_TOKEN'),
        'paths' => [
            'check_invoice' => env('SANF_SBF_CHECK_INVOICE_PATH', '/standby_financing/check_invoice'),
            'submit_pengajuan' => env('SANF_SBF_SUBMIT_PENGAJUAN_PATH', '/standby_financing/submit'),
        ],
    ],

];
