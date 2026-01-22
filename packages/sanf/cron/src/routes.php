<?php

use Illuminate\Support\Facades\Route;

Route::post('cron/payment-expire', [
    'as' => 'cron.payment.expire',
    'uses' => 'Payment\Controllers\CronPaymentController@paymentExpireCheck',
    'middleware' => 'throttle:1,1',
]);

Route::post('cron/payment-resubmit-pending-installment', [
    'as' => 'cron.payment.resubmit-pending-installment',
    'uses' => 'Payment\Controllers\CronPaymentController@resubmitPendingInstallment',
    'middleware' => 'throttle:1,1',
]);

Route::post('cron/payment-almost-expired', [
    'as' => 'cron.payment.almost-expired',
    'uses' => 'Payment\Controllers\CronPaymentController@paymentAlmostExpiredCheck',
    'middleware' => 'throttle:5,30',
]);
