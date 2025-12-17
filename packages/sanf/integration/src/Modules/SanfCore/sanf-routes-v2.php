<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiProcessorV2;

Route::group(config('sanf-api-v2.url'), [SanfCoreApiProcessorV2::class], function () {
    // spare part disbursement
    Route::get('spare-part-disbursement.list', '/api/sparepart_financing/invoice');
    Route::get('spare-part-disbursement.detail', '/api/sparepart_financing/invoice/{batchId}/{customerId}');

    // contract
    Route::get('contract.detail', '/api/kontrak/detail/{contractNumber}');

    // plafond
    Route::get('plafond.spare-part.list', '/api/plafond/list_sparepart');
});
