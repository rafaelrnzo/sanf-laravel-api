<?php

return [
    'mail_to_admin' => env('MAIL_TO_ADMIN'),
    'mail_to' => [
        'marketing' => env('MAIL_TO_MARKETING'),
        'service' => env('MAIL_TO_SERVICE'),
        'customer_service' => env('MAIL_TO_CUSTOMER_SERVICE'),
        'it_helpdesk' => env('MAIL_TO_IT_HELPDESK'),
    ],
];
