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

    Route::post('/asset', [
        'as' => 'upload',
        'uses' => 'Common\UploadFileController@process'
    ]);

    Route::group([
        'as' => 'contact-us',
        'prefix' => 'contact-us'],
        function () {

            Route::get('/topics', [
                'as' => 'topic',
                'uses' => 'ContactUs\ListAskUsTopicController@process'
            ]);
        });
});
