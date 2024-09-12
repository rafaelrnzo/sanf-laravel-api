<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/external', 'middleware' => ['basic-auth-config:core-h2h-user-provider']], function () {
    Route::post('push-notifications', ['as' => 'push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);
    Route::post('frequently-ask-questions/categories', ['as' => 'faq.category.add', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@postAdd']);
    Route::get('frequently-ask-questions/categories', ['as' => 'faq.category.browse', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@getBrowse']);
    Route::post('frequently-ask-questions/categories/{xid}/update', ['as' => 'faq.category.update', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@putUpdate']);
    Route::post('frequently-ask-questions/categories/{xid}/delete', ['as' => 'faq.category.delete', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@delete']);
    Route::post('frequently-ask-questions', ['as' => 'faq.add', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@postAdd']);
    Route::get('frequently-ask-questions', ['as' => 'faq.browse', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@getBrowse']);
    Route::post('frequently-ask-questions/{xid}/update', ['as' => 'faq.update', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@putUpdate']);
    Route::post('frequently-ask-questions/{xid}/delete', ['as' => 'faq.delete', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@delete']);
    Route::get('on-boardings', ['as' => 'on-boarding.browse', 'uses' => 'Setting\Controllers\OnBoardingByExternalController@getBrowse']);
    Route::post('on-boardings/{xid}', ['as' => 'on-boarding.update', 'uses' => 'Setting\Controllers\OnBoardingByExternalController@postUpdate']);

    Route::get('user-delete-accounts', ['as' => 'user-delete-accounts.browse', 'uses' => 'User\Controllers\UserAuthLogControllerByExternal@getBrowse']);
    Route::post('user-delete-accounts/{xid}/approve', ['as' => 'user-delete-accounts.approve', 'uses' => 'User\Controllers\UserAuthLogControllerByExternal@postApprove']);
    Route::post('user-delete-accounts/{xid}/reject', ['as' => 'user-delete-accounts.reject', 'uses' => 'User\Controllers\UserAuthLogControllerByExternal@postReject']);
    Route::post('plafonds/disbursements', ['as' => 'plafond.disbursement.update', 'uses' => 'Plafond\Controllers\PlafondDisbursementController@update']);
    Route::post('partners', ['as' => 'customer.add', 'uses' => 'Customer\Controllers\CustomerController@postAdd']);

    Route::post('users', ['as' => 'users.add', 'uses' => 'User\Controllers\CoreAccountController@addByScanina']);
    Route::post('users/availabilities', ['as' => 'users.availabilities', 'uses' => 'User\Controllers\CoreAccountController@browseByScanina']);
    Route::post('users/financing-application', ['as' => 'users.financing-application.add', 'uses' => 'Financing\Controllers\FinancingApplicationController@addByScanina']);

    Route::post('esign/check', ['as' => 'users.e-sign.check', 'uses' => 'Contract\ESignDocumentAdInsController@check']);
});

Route::group(['prefix' => 'v1/public'], function () {
    Route::get('projects/{xid}/approve', ['as' => 'projects.approve', 'uses' => 'Project\ProjectByExternalController@postApproveByExternal']);
    Route::get('projects/{xid}/reject', ['as' => 'projects.reject', 'uses' => 'Project\ProjectByExternalController@postRejectByExternal']);

    Route::get('commodities/{xid}/approve', ['as' => 'commodities.approve', 'uses' => 'Commodity\CommodityByExternalController@postApproveByExternal']);
    Route::get('commodities/{xid}/reject', ['as' => 'commodities.reject', 'uses' => 'Commodity\CommodityByExternalController@postRejectByExternal']);

    Route::post('push-notifications', ['as' => 'public.push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);

    Route::post('maintenance/up', ['as' => 'setting.maintenance.up', 'uses' => 'Setting\Controllers\MaintenanceModeByExternalController@postUp']);
    Route::post('maintenance/down', ['as' => 'setting.maintenance.down', 'uses' => 'Setting\Controllers\MaintenanceModeByExternalController@postDown']);
});

Route::group(['middleware' => ['http-logger', 'callback:tekenaja-provider']], function () {
    Route::post('v1/public/tekenaja/callback', ['as' => 'tekenaja.callback', 'uses' => 'Contract\ESignDocumentByExternalController@postCallback']);
});

//
Route::group(['middleware' => ['http-logger']], function () {
    Route::post('v1/public/ad-ins/esign-hub/callback', ['as' => 'ad-ins.esign-hub.callback', 'uses' => 'Contract\ESignDocumentAdInsController@callback']);
});
