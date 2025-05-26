<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\Fineksi\FineksiProcessor;

Route::group(config('fineksi-api.url'), [FineksiProcessor::class], function () {
    Route::post('document.scan.fineksi', 'invoice/extract');
});
