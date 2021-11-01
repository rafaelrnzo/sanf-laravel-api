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

Route::group(['prefix' => 'v1/external',], function () {
    Route::get('projects/{xid}/approve', ['as' => 'projects.approve', 'uses' => 'Project\ProjectByExternalController@postApproveByExternal']);
    Route::get('projects/{xid}/reject', ['as' => 'projects.reject', 'uses' => 'Project\ProjectByExternalController@postRejectByExternal']);
    Route::get('commodities/{xid}/approve', ['as' => 'commodities.approve', 'uses' => 'Commodity\CommodityByExternalController@postApproveByExternal']);
    Route::get('commodities/{xid}/reject', ['as' => 'commodities.reject', 'uses' => 'Commodity\CommodityByExternalController@postRejectByExternal']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth'], function () {

    Route::post('assets', ['as' => 'assets.upload', 'uses' => 'Asset\AssetFileController@upload']);

    Route::get('products', ['as' => 'products.list', 'uses' => 'Product\ListProductController@process']);

    Route::get('contact-us/topics', ['as' => 'contact-us.topic', 'uses' => 'ContactUs\ListAskUsTopicController@process']);
    Route::post('contact-us/ask-us', ['as' => 'contact-us.ask-us', 'uses' => 'ContactUs\AskUsSubmitController@process']);

    Route::get('branch-offices', ['as' => 'branch.list', 'uses' => 'Branch\ListBranchController@process']);

    Route::get('customer-positions', ['as' => 'customer.positions', 'uses' => 'User\Controllers\PositionController@getList']);

    Route::get('provinces', ['as' => 'provinces.list', 'uses' => 'Location\CoreLocationController@provinces']);
    Route::get('provinces/{province_id}/cities', ['as' => 'cities.list', 'uses' => 'Location\CoreLocationController@cities']);
    Route::get('provinces/{province_id}/cities/{city_id}/districts', ['as' => 'districts.list', 'uses' => 'Location\CoreLocationController@districts']);
    Route::get('provinces/{province_id}/cities/{city_id}/districts/{district_name}', ['as' => 'sub-districts.list', 'uses' => 'Location\CoreLocationController@subDistricts']);

    Route::post('users/register-with-contract', ['as' => 'users.register-with-contract', 'uses' => 'User\Controllers\ProfileController@postRegisterWithContract']);
    Route::get('users/profiles', ['as' => 'users.profiles.list', 'uses' => 'User\Controllers\ProfileController@getList']);
    Route::get('users/profiles/{xid}', ['as' => 'users.profiles.detail', 'uses' => 'User\Controllers\ProfileController@getDetail']);
    Route::put('users/profiles/{xid}', ['as' => 'users.profiles.update', 'uses' => 'User\Controllers\ProfileController@putUpdatePersonalProfile']);
    Route::post('users/profiles/{xid}/company', ['as' => 'users.profiles.company.create', 'uses' => 'User\Controllers\ProfileController@postCreateCompanyProfile']);
    Route::put('users/profiles/{xid}/company', ['as' => 'users.profiles.company.update', 'uses' => 'User\Controllers\ProfileController@putUpdateCompanyProfile']);
    Route::post('users/profiles/{xid}/switch', ['as' => 'users.profiles.switch', 'uses' => 'User\Controllers\ProfileController@postSwitch']);

    Route::post('users/profiles/{xid}/shareholders', ['as' => 'users.shareholders.create', 'uses' => 'Shareholder\ShareholderController@postCreate']);
    Route::get('users/profiles/{xid}/shareholders', ['as' => 'users.shareholders.list', 'uses' => 'Shareholder\ShareholderController@getList']);
    Route::get('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.detail', 'uses' => 'Shareholder\ShareholderController@getDetail']);
    Route::put('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.update', 'uses' => 'Shareholder\ShareholderController@putUpdate']);
    Route::delete('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.delete', 'uses' => 'Shareholder\ShareholderController@delete']);

    Route::get('users/profiles/{xid}/staffs', ['as' => 'users.staffs.list', 'uses' => 'Staff\StaffController@getList']);
    Route::get('users/profiles/{xid}/staffs/invited', ['as' => 'users.staffs.list-invited', 'uses' => 'Staff\StaffController@getInvitedList']);
    Route::post('users/profiles/{xid}/staffs/{no}/activate', ['as' => 'users.staffs.activate', 'uses' => 'Staff\StaffController@postActivate']);
    Route::post('users/profiles/{xid}/staffs/{no}/deactivate', ['as' => 'users.staffs.deactivate', 'uses' => 'Staff\StaffController@postDeactivate']);

    Route::get('customer-titles', ['as' => 'customer.titles', 'uses' => 'User\Controllers\TitleController@getList']);

    Route::get('news', ['as' => 'news.list', 'uses' => 'News\NewsController@getList']);

    Route::get('promo', ['as' => 'promo.list', 'uses' => 'Promo\PromoController@getList']);

    Route::get('astra-products', ['as' => 'astra.product.list', 'uses' => 'Astra\ProductAstraListController@getList']);

    Route::get('locations', ['as' => 'locations.list', 'uses' => 'Location\LocationController@getList']);

    Route::get('users/metadata-info', ['as' => 'users.metadata-info', 'uses' => 'User\Controllers\UserController@getProjectMetadataInfo']);

    Route::get('projects', ['as' => 'projects.list', 'uses' => 'Project\ProjectController@getList']);
    Route::get('projects/{xid}', ['as' => 'projects.detail', 'uses' => 'Project\ProjectController@getDetail']);
    Route::get('users/projects', ['as' => 'users.projects.list', 'uses' => 'Project\ProjectController@getListByUser']);
    Route::post('users/projects', ['as' => 'users.projects.list', 'uses' => 'Project\ProjectController@postCreateByUser']);
    Route::get('users/projects/{xid}', ['as' => 'users.projects.detail', 'uses' => 'Project\ProjectController@getDetailByUser']);
    Route::put('users/projects/{xid}', ['as' => 'users.projects.update', 'uses' => 'Project\ProjectController@putUpdateByUser']);
    Route::delete('users/projects/{xid}', ['as' => 'users.projects.delete', 'uses' => 'Project\ProjectController@deleteByUser']);
    Route::post('users/projects/{xid}/publish', ['as' => 'users.projects.publish', 'uses' => 'Project\ProjectController@postPublishByUser']);
    Route::post('users/projects/{xid}/unpublish', ['as' => 'users.projects.unpublish', 'uses' => 'Project\ProjectController@postUnpublishByUser']);

    Route::get('commodities', ['as' => 'commodities.list', 'uses' => 'Commodity\CommodityController@getList']);
    Route::get('commodities/{xid}', ['as' => 'commodities.detail', 'uses' => 'Commodity\CommodityController@getDetail']);
    Route::get('users/commodities', ['as' => 'users.commodities.list', 'uses' => 'Commodity\CommodityController@getListByUser']);
    Route::post('users/commodities', ['as' => 'users.commodities.list', 'uses' => 'Commodity\CommodityController@postCreateByUser']);
    Route::get('users/commodities/{xid}', ['as' => 'users.commodities.detail', 'uses' => 'Commodity\CommodityController@getDetailByUser']);
    Route::put('users/commodities/{xid}', ['as' => 'users.commodities.update', 'uses' => 'Commodity\CommodityController@putUpdateByUser']);
    Route::delete('users/commodities/{xid}', ['as' => 'users.commodities.delete', 'uses' => 'Commodity\CommodityController@deleteByUser']);
    Route::post('users/commodities/{xid}/publish', ['as' => 'users.commodities.publish', 'uses' => 'Commodity\CommodityController@postPublishByUser']);
    Route::post('users/commodities/{xid}/unpublish', ['as' => 'users.commodities.unpublish', 'uses' => 'Commodity\CommodityController@postUnpublishByUser']);

    Route::get('users/personal-assistants', ['as' => 'users.metadata-info', 'uses' => 'User\Controllers\UserController@getPersonalAssistant']);

    Route::get('financing-object-brands', ['as' => 'financing.personal.facility.brand', 'uses' => 'Financing\Controllers\FinancingObjectController@getBrands']);
    Route::get('financing-object-brands/{brand_id}/types', ['as' => 'financing.personal.facility.type', 'uses' => 'Financing\Controllers\FinancingObjectController@getTypes']);
    Route::get('financing-object-brands/{brand_id}/types/{type_id}/models', ['as' => 'financing.personal.facility.models', 'uses' => 'Financing\Controllers\FinancingObjectController@getModels']);
    Route::get('financing-methods', ['as' => 'financing-method.list', 'uses' => 'Financing\FinancingController@getList']);
    Route::get('financing-facilities', ['as' => 'financing-facilities.list', 'uses' => 'Financing\FinancingController@getListFacilities']);
    Route::get('financing-facilities/{id}/methods', ['as' => 'financing-facilities.method.list', 'uses' => 'Financing\FinancingController@getListMethodsByFacility']);
    Route::get('financing-prerequisites', ['as' => 'financing-prerequisites.list', 'uses' => 'Financing\FinancingController@getPrerequisiteList']);

    Route::get('users/profile/{xid}/has-valid-ktp', ['as' => 'financing.personal.validate.ktp', 'uses' => 'Financing\Controllers\FinancingCompletionController@validateKtp']);
    Route::get('users/profile/{xid}/has-valid-npwp', ['as' => 'financing.company.validate.ktp', 'uses' => 'Financing\Controllers\FinancingCompletionController@validateNpwp']);

    Route::post('users/profile/{xid}/assets', ['as' => 'financing.assets', 'uses' => 'Financing\Controllers\FinancingDocumentController@upload']);
});
