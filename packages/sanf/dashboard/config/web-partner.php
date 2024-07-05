<?php

return [
    'base_url' => env('WEB_PARTNER_URL', 'http://localhost'),
    'app_name' => env('WEB_PARTNER_APP', 'SANFIND Web Partner'),
    'admin_id' => env('ADMIN_ID', 1),
    'user' => [
        'entity' => [
            'customer_id' => env('CUSTOMER_ENTITY_ID', 4),
        ],
        'status' => [
            'pending_id' => env('PENDING_STATUS_ID', 1),
        ],
        'verification' => [
            'expire_days' => env('VERIFICATION_EXPIRE_DAYS', 7),
        ],
    ],
];
