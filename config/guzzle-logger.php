<?php
return [
    'logger' => env('GUZZLE_LOGGER', false),
    'driver' => 'database', # log, database
    'censor' => [
        'replacement' => '**censor**',
        'bad-keys' => [
            'client-id',
            'authorization',
            'password',
        ],
        /*TODO IMPROVE CENSORING VALUE
        'bad-values' => []
         */
    ],
];
