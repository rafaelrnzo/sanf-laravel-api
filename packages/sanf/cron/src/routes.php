<?php

use Illuminate\Support\Facades\Route;

Route::post('cron/payment-expire', [
    'as' => 'cron.payment.expire',
    'uses' => 'Payment\Controllers\CronPaymentController@paymentExpireCheck',
    'middleware' => 'throttle:1,1',
]);
