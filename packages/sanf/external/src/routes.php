<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/external', 'middleware' => ['basic-auth-config:core-h2h-user-provider']], function () {
    Route::post('push-notifications', ['as' => 'push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);
    Route::post('frequently-ask-questions/categories', ['as' => 'faq.category.add', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@postAdd']);
    Route::get('frequently-ask-questions/categories', ['as' => 'faq.category.browse', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@getBrowse']);
    Route::put('frequently-ask-questions/categories/{xid}', ['as' => 'faq.category.update', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@putUpdate']);
    Route::delete('frequently-ask-questions/categories/{xid}', ['as' => 'faq.category.delete', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionCategoryByExternalController@delete']);
    Route::post('frequently-ask-questions', ['as' => 'faq.add', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@postAdd']);
    Route::get('frequently-ask-questions', ['as' => 'faq.browse', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@getBrowse']);
    Route::put('frequently-ask-questions/{xid}', ['as' => 'faq.update', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@putUpdate']);
    Route::delete('frequently-ask-questions/{xid}', ['as' => 'faq.delete', 'uses' => 'Setting\Controllers\FrequentlyAskQuestionByExternalController@delete']);
    Route::get('on-boardings', ['as' => 'on-boarding.browse', 'uses' => 'Setting\Controllers\OnBoardingByExternalController@getBrowse']);
    Route::post('on-boardings/{xid}', ['as' => 'on-boarding.update', 'uses' => 'Setting\Controllers\OnBoardingByExternalController@postUpdate']);
});

Route::group(['prefix' => 'v1/public'], function () {
    Route::get('projects/{xid}/approve', ['as' => 'projects.approve', 'uses' => 'Project\ProjectByExternalController@postApproveByExternal']);
    Route::get('projects/{xid}/reject', ['as' => 'projects.reject', 'uses' => 'Project\ProjectByExternalController@postRejectByExternal']);

    Route::get('commodities/{xid}/approve', ['as' => 'commodities.approve', 'uses' => 'Commodity\CommodityByExternalController@postApproveByExternal']);
    Route::get('commodities/{xid}/reject', ['as' => 'commodities.reject', 'uses' => 'Commodity\CommodityByExternalController@postRejectByExternal']);

    Route::post('push-notifications', ['as' => 'public.push-notifications.add', 'uses' => 'Notification\PushNotificationByExternalController@postAdd']);
});

Route::group(['middleware' => ['http-logger', 'callback:tekenaja-provider']], function () {
    Route::post('v1/public/tekenaja/callback', ['as' => 'tekenaja.callback', 'uses' => 'Contract\ESignDocumentByExternalController@postCallback']);
});
