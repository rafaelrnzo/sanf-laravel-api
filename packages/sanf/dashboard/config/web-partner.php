<?php

return [
    'base_url' => env('WEB_PARTNER_URL', 'http://localhost'),
    'admin_id' => env('ADMIN_ID', 1),
    'user' => [
        'entity' => [
            'customer_id' => env('CUSTOMER_ENTITY_ID', 4),
        ],
        'status' => [
            'pending_id' => env('PENDING_STATUS_ID', 1),
        ],
    ],
];
