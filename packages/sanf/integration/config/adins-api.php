<?php

return [
    'host' => env('ADINS_HOST', 'http://localhost'),
    'e-sign-hub' => [
        'path_url' => env('ADINS_ESIGN_PATH_URL', ''),
        'key' => env('ADINS_ESIGN_KEY', 'xxx'),
        'tenant_code' => env('ADINS_ESIGN_TENANT_CODE', 'xxx'),
        'psre_code' => env('ADINS_ESIGN_PSRE_CODE', null),
    ],
];
