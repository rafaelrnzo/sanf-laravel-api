<?php

return [
    'logger' => env('GUZZLE_LOGGER', false),
    'driver' => 'database', // log, database
    'censor' => [
        'replacement' => '**censor**',
        'bad-keys' => [
            'client-id',
            'authorization',
            'password',
            'x-api-key',
            'account_number',
            'bank_account_number',
            'owner',
            'bank_owner',
            'npwp',
            'ktp',
            'nik',
        ],
        /*TODO IMPROVE CENSORING VALUE
        'bad-values' => []
         */
    ],
];
