<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\Nanonets\NanonetsProcessor;

Route::group(config('nanonets-api.url'), [NanonetsProcessor::class], function () {
    Route::post('document.scan', 'v2/OCR/Model/{uuid}/LabelFile/');
});
