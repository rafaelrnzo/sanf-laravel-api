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

Route::group(['prefix' => 'v1', 'middleware' => 'auth'], function () {

    Route::post('assets', ['as' => 'assets.upload', 'uses' => 'Common\UploadFileController@process']);

    Route::get('products', ['as' => 'products.list', 'uses' => 'Product\ListProductController@process']);

    Route::get('contact-us/topics', ['as' => 'contact-us.topic', 'uses' => 'ContactUs\ListAskUsTopicController@process']);
    Route::post('contact-us/ask-us', ['as' => 'contact-us.ask-us', 'uses' => 'ContactUs\AskUsSubmitController@process']);

    Route::get('branch-offices', ['as' => 'branch.list', 'uses' => 'Branch\ListBranchController@process']);
});

Route::get('v1/test', function (){
//    $response = (new \Sanf\Integration\InternalApiClient())->findCustomerByEmail('rossannalie@gmail.com');
//    dd($response);
//    $response = \NbsPhp\ApiWrapper\Api\Request::route('customer.find-by-id')
//        ->pathParams(['id' => 1])
//        ->send();
//    return $response->json();
});
