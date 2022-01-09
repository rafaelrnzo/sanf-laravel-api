<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/external'], function () {
    Route::get('projects/{xid}/approve', ['as' => 'projects.approve', 'uses' => 'Project\ProjectByExternalController@postApproveByExternal']);
    Route::get('projects/{xid}/reject', ['as' => 'projects.reject', 'uses' => 'Project\ProjectByExternalController@postRejectByExternal']);

    Route::get('commodities/{xid}/approve', ['as' => 'commodities.approve', 'uses' => 'Commodity\CommodityByExternalController@postApproveByExternal']);
    Route::get('commodities/{xid}/reject', ['as' => 'commodities.reject', 'uses' => 'Commodity\CommodityByExternalController@postRejectByExternal']);

    Route::post('push-notifications', ['as' => 'push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);
});
