<?php
return [
    'enabled' => env('HTTP_LOGGER', false),
    /*TODO IMPROVE CUSTOM DRIVER
    'driver' => 'database', # log, database, etc...
    */
    'censor' => [
        'replacement' => '**censor**',
        'bad-keys' => [
            'authorization',
            'password',
            'password_confirmation',
            'auth_token',
            'notification_token',
            'token',
        ],
        /*TODO IMPROVE CENSORING VALUE
        'bad-values' => []
         */
    ],
];
