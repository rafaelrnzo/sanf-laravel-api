<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\TekenAjaInternalApiProcessor;

Route::group(config('tekenaja-internal.url'), [TekenAjaInternalApiProcessor::class], function () {
    Route::get('location.province', 'v2/data/province');
    Route::get('location.district', 'v2/data/district');
    Route::get('location.subdistrict', 'v2/data/subdistrict');
});