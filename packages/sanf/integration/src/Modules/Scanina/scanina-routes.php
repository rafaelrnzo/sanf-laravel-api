<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\Scanina\ScaninaApiProcessor;

Route::group(config('scanina-api.url'), [ScaninaApiProcessor::class], function () {
    Route::get('scanina.product.buy.browse', 'v1/buy/list-products');
    Route::get('scanina.product.rent.browse', 'v1/rental/list-products');
});
