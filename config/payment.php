<?php

return [
    'currency' => 'IDR',

    /*
     * Expire in minutes
     */
    'expire_in' => env('PAYMENT_EXPIRE_IN', 1440),

    /*
     * Interval in minutes
     */
    'status_check_interval' => 10,
];
