<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/external', 'middleware' => 'basic-auth-config:core-h2h-user-provider'], function () {
    Route::post('push-notifications', ['as' => 'push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);
});

Route::group(['prefix' => 'v1/public'], function () {
    Route::get('projects/{xid}/approve', ['as' => 'projects.approve', 'uses' => 'Project\ProjectByExternalController@postApproveByExternal']);
    Route::get('projects/{xid}/reject', ['as' => 'projects.reject', 'uses' => 'Project\ProjectByExternalController@postRejectByExternal']);

    Route::get('commodities/{xid}/approve', ['as' => 'commodities.approve', 'uses' => 'Commodity\CommodityByExternalController@postApproveByExternal']);
    Route::get('commodities/{xid}/reject', ['as' => 'commodities.reject', 'uses' => 'Commodity\CommodityByExternalController@postRejectByExternal']);

    Route::post('push-notifications', ['as' => 'public.push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);

    Route::post('e-sign/register-verified', ['as' => 'e-sign.verified', 'uses' => 'Contract\ESignDocumentByExternalController@postHasVerified']);
    Route::post('e-sign/document-complete', ['as' => 'e-sign.document-complete', 'uses' => 'Contract\ESignDocumentByExternalController@postDocumentComplete']);
});
