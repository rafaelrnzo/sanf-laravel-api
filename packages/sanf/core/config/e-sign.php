<?php

return [
    /*
     * Sign status check interval in seconds.
     */
    'sign_status_check_interval' => env('ESIGN_STATUS_CHECK_INTERVAL', 60),

    /*
     * Sign status check window expired in minutes.
     * Status can only be checked within x minutes after signed.
     */
    'sign_status_check_window_expired' => env('ESIGN_STATUS_CHECK_WINDOW_EXPIRED', 30),
];
