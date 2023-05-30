<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\Scanina\ScaninaApiProcessor;

Route::group(config('scanina-api.url'), [ScaninaApiProcessor::class], function () {
    Route::post('scanina.user.account.add-cart', '/v1/submit-request');

    Route::get('scanina.product.buy.browse', '/v1/product/buy');
    Route::get('scanina.product.buy.read', '/v1/product/buy/{xid}');
    Route::get('scanina.product.buy.category.browse', '/v1/category/buy');
    Route::get('scanina.product.buy.brand.browse', '/v1/brand/buy');
    Route::get('scanina.product.buy.type.browse', '/v1/type/buy');
    Route::get('scanina.product.buy.model.browse', '/v1/model/buy');

    Route::get('scanina.product.rent.browse', '/v1/product/rental');
    Route::get('scanina.product.rent.read', '/v1/product/rental/{xid}');
    Route::get('scanina.product.rent.category.browse', '/v1/category/rental');
    Route::get('scanina.product.rent.brand.browse', '/v1/brand/rental');
    Route::get('scanina.product.rent.type.browse', '/v1/type/rental');
    Route::get('scanina.product.rent.model.browse', '/v1/model/rental');

    Route::get('scanina.product.specification.browse', '/v1/product/specification');

    Route::get('scanina.product.service.browse', '/v1/product/service');
    Route::get('scanina.product.service.read', '/v1/product/service/{xid}');
    Route::get('scanina.product.service.category.browse', '/v1/category/service');

    Route::get('scanina.product.spare-part.browse', '/v1/product/spare-part');
    Route::get('scanina.product.spare-part.read', '/v1/product/spare-part/{xid}');
    Route::get('scanina.product.spare-part.category.browse', '/v1/category/spare-part');
    Route::get('scanina.product.spare-part.brand.browse', '/v1/brand/spare-part');


    Route::get('scanina.city.browse', '/v1/list-cities');
});
