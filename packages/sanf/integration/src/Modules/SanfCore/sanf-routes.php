<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiProcessor;

Route::group(config('sanf-api.url'), [SanfCoreApiProcessor::class], function () {
    Route::post('customer.find-by-email', '/Login');
    Route::get('customer.find-by-id', '/Login/customer/{id}');
    Route::get('customer.find-by-email-and-npwp', '/customer/profil/{email}/{npwp}');
    Route::post('customer.register', '/Customer/registernewuser');
    Route::post('customer.update', '/customer/userupdate');
    Route::post('customer.create-company', '/customer/tambahperusahaan');

    Route::post('customer.shareholder.update', 'customer/pemegangsahamupdate');
    Route::post('customer.shareholder.delete', 'customer/pemegangsahamdelete');
    Route::post('customer.shareholder.create', 'customer/pemegangsaham');
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

    Route::get('plafond.list', '/Plafond/Header');
    Route::get('plafond.list-by-type', '/Plafond/detail');
    Route::get('plafond.factoring', '/Plafond/detailfactoring');
    Route::post('plafond.create', '/Plafond');
    Route::get('plafond.history', '/plafond/history');
    Route::post('plafond.disbursement.create', '/Plafond/Disburstment');
    Route::get('plafond.disbursement.browse', '/Plafond/Listpencairan');

    // V2
    Route::get('v2.plafond.factoring', '/V2/plafond/detail_factoring/{cust_id}/{plafond_code}');
    Route::get('v2.plafond.bowheer', '/V2/plafond/bowheer_list/{cust_id}/{bowheer_code}');
    Route::get('sanf-internal.sbf.plafond-header', '/V2/plafond/{custId}');

    Route::get('bowheer.browse', '/Plafond/Bowheerlist');

    Route::get('bank.account', '/Plafond/Bankaccount');
    Route::get('ocr.permission', '/Plafond/Ocrpermission');

    Route::get('prepayment.contract.list', '/Prepayment/header');
    Route::get('prepayment.detail', '/Prepayment');

    Route::get('contract.metadata', '/Kontrak/Dashboard');
    Route::get('contract.account-receivable', '/Kontrak/DashboardDetail');
    Route::get('contracts', '/Kontrak');
    Route::get('contracts.detail', '/Kontrak/DetailKontrak');
    Route::get('contracts.financing-unit.item', '/Kontrak/ItemKontrak');
    Route::get('contracts.financing-unit.invoice', '/Kontrak/TagihanKontrak');
    Route::get('contracts.pdc', '/PDC');
    Route::get('v2.contracts.pdc', '/V2/giro/kontrak/{cust_id}'); // V2 CR2025
    Route::get('contracts.pdc.detail', '/PDC/detail');
    Route::get('v2.contracts.pdc.detail', '/V2/giro/giroByKontrak/{cust_id}'); // V2 CR2025
    Route::get('contracts.financing-unit-submission', '/UnitPembiayaan');
    Route::get('contracts.financing-unit-submission.item', '/UnitPembiayaan/detail');

    Route::get('location.all-cities', '/Address/allcity');
    Route::get('v2.location.all-cities', '/V2/insurance/city'); // V2 CR2025

    Route::get('invoice-collections.financing-units.list', '/PengambilanInv');
    Route::get('v2.invoice-collections.financing-units.list', '/V2/invoice/{cust_id}'); // V2 CR2025
    Route::get('insurances.financing-units.list', '/Insurance');
    Route::get('v2.insurances.financing-units.list', '/V2/insurance/{cust_id}'); // V2 CR2025

    Route::get('assignee-survey', '/SelfSurvei/offline');
    Route::post('surveys.add', '/SelfSurvei');
    Route::get('surveys', '/SelfSurvei');

    Route::get('e-sign.user', '/esign/Pendaftaran');
    Route::post('e-sign.user.update-status', '/esign/pendaftaran');
    Route::get('e-sign.document.browse', '/esign/list');
    Route::post('e-sign.document.update-status', '/esign/fincall');
    Route::post('e-sign.document.update-file', '/esign/download');
    Route::get('e-sign.category.list', '/esign/kategori');

    Route::get('financing-applications.browse', '/Pengajuan/StatusPengajuan');

    // request unggah dokumen
    Route::get('request-document.browse', '/upload/listdoc');
    Route::get('request-uploaded-document.browse', '/upload/riwayatdoc');
    Route::post('request-document.submit', '/upload/submitdoc');
});
