<?php

return [
    'logger' => env('GUZZLE_LOGGER', false),
    /*TODO IMPROVE CUSTOM DRIVER
    'driver' => 'database', # log, database, etc...
    */
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
