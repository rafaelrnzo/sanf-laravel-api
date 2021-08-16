<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\InternalApiProcessor;

Route::group(config('sanf-internal.url'), [InternalApiProcessor::class], function () {
    Route::get('customer.find-by-email', 'Login/{email}');
    Route::get('customer.find-by-id', '/Login/customer/{id}');
    Route::get('customer.find-by-email-and-npwp', '/customer/profil/{email}/{npwp}');
    Route::post('customer.register', '/Customer/registernewuser');
    Route::post('customer.update', '/customer/userupdate');
    Route::post('customer.create-company', '/customer/tambahperusahaan');

    Route::post('customer.shareholder.update', 'customer/pemegangsahamupdate');
    Route::post('customer.shareholder.delete', 'customer/pemegangsahamdelete');
    Route::post('customer.shareholder.create', 'customer/pemegangsaham/{id}');
    Route::get('customer.shareholder.list', 'customer/pemegangsaham/{id}');

    Route::get('location.provinces', 'Address/provinsi');
    Route::get('location.cities', 'Address/kota/{province_id}');
    Route::get('location.districts', 'Address/kecamatan/{province_id}/{city_id}');
    Route::get('location.sub-districts', 'Address/Kelurahan/{province_id}/{city_id}/{district_id}');

    Route::get('customer.positions', 'customer/jabatan');
    Route::get('customer.titles', 'Customer/title/{type}');
});
