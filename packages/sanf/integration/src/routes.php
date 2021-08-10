<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\InternalApiProcessor;

Route::group(config('sanf-internal.url'), [InternalApiProcessor::class], function () {
    Route::get('customer.find-by-email', 'Login/{email}');
});
