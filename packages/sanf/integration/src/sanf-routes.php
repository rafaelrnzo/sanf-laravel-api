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

    Route::get('customer.staff.list', 'Customer/pengurus/{id}');

    Route::get('location.provinces', 'Address/provinsi');
    Route::get('location.cities', 'Address/kota/{province_id}');
    Route::get('location.districts', 'Address/kecamatan/{province_id}/{city_id}');
    Route::get('location.sub-districts', 'Address/Kelurahan/{province_id}/{city_id}/{district_id}');

    Route::get('customer.positions', 'customer/jabatan');
    Route::get('customer.titles', 'Customer/title/{type}');

    Route::get('financing-object.brand', 'Upload/brand');
    Route::get('financing-object.type', 'Upload/type/{brand_id}');
    Route::get('financing-object.model', 'Upload/model/{brand_id}/{type_id}');
    Route::get('financing-completion.ktp', 'Upload/CheckKTP/{customer_id}');
    Route::get('financing-completion.npwp', 'Upload/CheckNPWP/{customer_id}');

    Route::post('customer.upload', 'upload');

    Route::get('customer.plafond.list', '/Plafond/Header/{customer_id}');
    Route::get('customer.plafond.list-by-type', '/Plafond/Detail/{customer_id}/{p_code}');
    Route::post('customer.plafond.create', '/Plafond');
    Route::get('customer.plafond.history', '/plafond/history');

    Route::get('prepayment.contract.list', '/Prepayment/header');
    Route::get('prepayment.detail', '/Prepayment');

    Route::get('contract.metadata', '/Kontrak/Dashboard');
    Route::get('contract.account-receivable', '/Kontrak/DashboardDetail');
    Route::get('contracts', '/Kontrak');
    Route::get('contracts.detail', '/Kontrak/DetailKontrak');
    Route::get('contracts.financing-unit.item', '/Kontrak/ItemKontrak');
    Route::get('contracts.financing-unit.invoice', '/Kontrak/TagihanKontrak');
    Route::get('contracts.pdc', '/PDC');
    Route::get('contracts.pdc.detail', '/PDC/detail');
    Route::get('contracts.financing-unit-submission', '/UnitPembiayaan');
    Route::get('contracts.financing-unit-submission.item', '/UnitPembiayaan/detail');

    Route::get('location.all-cities', '/Address/allcity');

    Route::get('invoice-collections.financing-units.list', '/PengambilanInv');
    Route::get('insurances.financing-units.list', '/Insurance');

    Route::get('assignee-survey', '/SelfSurvei/offline');
    Route::post('surveys.add', '/SelfSurvei');
    Route::get('surveys', '/SelfSurvei');

    Route::get('e-sign.user', '/esign/Pendaftaran');
});
