<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiProcessorV2;

Route::group(config('sanf-api-v2.url'), [SanfCoreApiProcessorV2::class], function () {
    Route::get('spare-part-disbursement.list', '/api/sparepart_financing/invoice');
});
