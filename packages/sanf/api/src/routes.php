<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::group([
        'as' => 'products',
        'prefix' => 'products'],
        function () {
            Route::get('/', [
                'as' => 'list',
                'uses' => 'Product\ListProductController@process'
            ]);
        });


    Route::post('/asset', [
        'as' => 'upload',
        'uses' => 'Common\UploadFileController@process'
    ]);

    Route::group([
        'as' => 'web-view',
        'prefix' => 'web-view'],
        function () {

            Route::get('/about-us', [
                'as' => 'about-us',
                'uses' => 'Common\WebViewAboutUsController@process'
            ]);

        });

    Route::group([
        'as' => 'contact-us',
        'prefix' => 'contact-us'],
        function () {

            Route::get('/topics', [
                'as' => 'topic',
                'uses' => 'ContactUs\ListAskUsTopicController@process'
            ]);

            Route::post('/ask-us', [
                'as' => 'ask-us',
                'uses' => 'ContactUs\AskUsSubmitController@process'
            ]);
        });
});
