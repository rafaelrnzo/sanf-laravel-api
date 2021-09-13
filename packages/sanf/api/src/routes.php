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

    Route::get('customer-positions', ['as' => 'customer.positions', 'uses' => 'User\PositionController@getList']);

    Route::get('provinces', ['as' => 'provinces.list', 'uses' => 'Location\LocationController@provinces']);
    Route::get('provinces/{province_id}/cities', ['as' => 'cities.list', 'uses' => 'Location\LocationController@cities']);
    Route::get('provinces/{province_id}/cities/{city_id}/districts', ['as' => 'districts.list', 'uses' => 'Location\LocationController@districts']);
    Route::get('provinces/{province_id}/cities/{city_id}/districts/{district_name}', ['as' => 'sub-districts.list', 'uses' => 'Location\LocationController@subDistricts']);

    Route::post('users/register-with-contract', ['as' => 'users.register-with-contract', 'uses' => 'User\ProfileController@postRegisterWithContract']);
    Route::get('users/profiles', ['as' => 'users.profiles.list', 'uses' => 'User\ProfileController@getList']);
    Route::get('users/profiles/{xid}', ['as' => 'users.profiles.detail', 'uses' => 'User\ProfileController@getDetail']);
    Route::put('users/profiles/{xid}', ['as' => 'users.profiles.update', 'uses' => 'User\ProfileController@putUpdatePersonalProfile']);
    Route::post('users/profiles/{xid}/company', ['as' => 'users.profiles.company.create', 'uses' => 'User\ProfileController@postCreateCompanyProfile']);
    Route::put('users/profiles/{xid}/company', ['as' => 'users.profiles.company.update', 'uses' => 'User\ProfileController@putUpdateCompanyProfile']);
    Route::post('users/profiles/{xid}/switch', ['as' => 'users.profiles.switch', 'uses' => 'User\ProfileController@postSwitch']);

    Route::post('users/profiles/{xid}/shareholders', ['as' => 'users.shareholders.create', 'uses' => 'Shareholder\ShareholderController@postCreate']);
    Route::get('users/profiles/{xid}/shareholders', ['as' => 'users.shareholders.list', 'uses' => 'Shareholder\ShareholderController@getList']);
    Route::get('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.detail', 'uses' => 'Shareholder\ShareholderController@getDetail']);
    Route::put('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.update', 'uses' => 'Shareholder\ShareholderController@putUpdate']);
    Route::delete('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.delete', 'uses' => 'Shareholder\ShareholderController@delete']);

    Route::get('users/profiles/{xid}/staffs', ['as' => 'users.staffs.list', 'uses' => 'Staff\StaffController@getList']);
    Route::get('users/profiles/{xid}/staffs/invited', ['as' => 'users.staffs.list-invited', 'uses' => 'Staff\StaffController@getInvitedList']);
    Route::post('users/profiles/{xid}/staffs/{no}/activate', ['as' => 'users.staffs.activate', 'uses' => 'Staff\StaffController@postActivate']);
    Route::post('users/profiles/{xid}/staffs/{no}/deactivate', ['as' => 'users.staffs.deactivate', 'uses' => 'Staff\StaffController@postDeactivate']);

    Route::get('customer-titles', ['as' => 'customer.titles', 'uses' => 'User\TitleController@getList']);
});
