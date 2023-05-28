<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\Scanina\ScaninaApiProcessor;

Route::group(config('scanina-api.url'), [ScaninaApiProcessor::class], function () {
    Route::get('scanina.product.buy.browse', '/v1/buy/list-products');
    Route::get('scanina.product.buy.read', '/v1/buy/detail-product/{xid}');
    Route::get('scanina.product.buy.category.browse', '/v1/buy/list-categories');
    Route::get('scanina.product.buy.brand.browse', '/v1/buy/list-brand');
    Route::get('scanina.product.buy.type.browse', '/v1/buy/list-types');
    Route::get('scanina.product.buy.model.browse', '/v1/buy/list-models');

    Route::get('scanina.product.rent.browse', '/v1/rental/list-products');
    Route::get('scanina.product.rent.category.browse', '/v1/rental/list-categories');
    Route::get('scanina.product.rent.brand.browse', '/v1/rental/list-brand');
    Route::get('scanina.product.rent.type.browse', '/v1/rental/list-types');
    Route::get('scanina.product.rent.model.browse', '/v1/rental/list-models');

    Route::get('scanina.product.service.browse', '/v1/service/list-products');
    Route::get('scanina.product.service.category.browse', '/v1/service/list-categories');

    Route::get('scanina.product.spare-part.browse', '/v1/spare-part/list-products');
    Route::get('scanina.product.spare-part.category.browse', '/v1/spare-part/list-categories');
    Route::get('scanina.product.spare-part.brand.browse', '/v1/spare-part/list-brands');
});
