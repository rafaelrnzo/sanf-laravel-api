<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiProcessorV2;

Route::group(config('sanf-api-v2.url'), [SanfCoreApiProcessorV2::class], function () {
    // Spare Part Disbursement
    Route::get('sanf-internal-v2.spare-part-disbursement.list', '/api/sparepart_financing/invoice');
    Route::get('sanf-internal-v2.spare-part-disbursement.detail', '/api/sparepart_financing/invoice/{batchId}/{customerId}');
    Route::post('sanf-internal-v2.spare-part-disbursement.submit', '/api/sparepart_financing/invoice/submit');

    // Contract
    Route::get('sanf-internal-v2.contract.detail', '/api/kontrak/detail/{contractNumber}');

    // Plafond
    Route::get('sanf-internal-v2.plafond.spare-part.list', '/api/plafond/list_sparepart');

    // Installment
    Route::get('sanf-internal-v2.installment.summary', '/api/tagihan/summary');
    Route::get('sanf-internal-v2.installment.list', '/api/tagihan/list');
    Route::get('sanf-internal-v2.installment.detail', '/api/tagihan/detail/{no_kontrak}/{jatuh_tempo}');
});
