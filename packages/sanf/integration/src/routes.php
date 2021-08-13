<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\InternalApiProcessor;

Route::group(config('sanf-internal.url'), [InternalApiProcessor::class], function () {
    Route::get('customer.find-by-email', 'Login/{email}');
    Route::get('customer.find-by-id', '/Login/customer/{id}');

    Route::get('location.provinces', 'Address/provinsi');
    Route::get('location.cities', 'Address/kota/{province_id}');
    Route::get('location.districts', 'Address/kecamatan/{province_id}/{city_id}');
    Route::get('location.sub-districts', 'Address/Kelurahan/{province_id}/{city_id}/{district_id}');

    Route::get('customer.positions', 'customer/jabatan');
    Route::get('customer.titles', 'Customer/title/{type}');
});
