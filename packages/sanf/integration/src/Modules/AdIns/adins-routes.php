<?php

use NbsPhp\ApiWrapper\Api\Route;
use Sanf\Integration\Modules\AdIns\AdInsESignApiProcessor;

$url = config('adins.host') . config('adins.e-sign-hub.path_url');
Route::group($url, [AdInsESignApiProcessor::class], function () {
    Route::post('user.register.submit', 'user/register');
    Route::post('user.register.check', 'user/checkRegistration');
    Route::post('user.otp.request', 'user/sentOtpSigning');
    Route::post('document.sign.submit', 'document/signDocument');
    Route::post('document.sign.check', 'document/checkStatusSigning');
    Route::post('document.sign.download', 'document/downloadDocument');
});
