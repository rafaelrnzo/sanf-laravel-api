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

Route::get('web-view/about-us', ['as' => 'web-view.about-us', 'uses' => 'Setting\Controllers\WebViewController@aboutUs']);
Route::get('web-view/terms-and-condition', ['as' => 'web-view.terms-and-condition', 'uses' => 'Setting\Controllers\WebViewController@termsCondition']);
Route::get('web-view/privacy-policy', ['as' => 'web-view.privacy-policy', 'uses' => 'Setting\Controllers\WebViewController@privacyPolicy']);

Route::get('web-view/faq', ['as' => 'web-view.faq', 'uses' => 'Setting\Controllers\WebViewController@browseFrequentlyAskQuestion']);
Route::get('web-view/faq/popular', ['as' => 'web-view.faq-popular', 'uses' => 'Setting\Controllers\WebViewController@browsePopularFrequentlyAskQuestion']);
Route::get('web-view/faq/{categoryId}', ['as' => 'web-view.faq-by-category', 'uses' => 'Setting\Controllers\WebViewController@browseFrequentlyAskQuestionByCategory']);

Route::get('web-view/pages/{xid}', ['as' => 'web-view.static-content.read', 'uses' => 'Setting\Controllers\StaticContentController@getRead']);

Route::get('pages/about-us', ['as' => 'web-view.about-us', 'uses' => 'Setting\Controllers\WebViewController@aboutUs']);
Route::get('pages/terms-and-condition', ['as' => 'web-view.terms-and-condition', 'uses' => 'Setting\Controllers\WebViewController@termsCondition']);
Route::get('pages/privacy-policy', ['as' => 'web-view.privacy-policy', 'uses' => 'Setting\Controllers\WebViewController@privacyPolicy']);

Route::get('pages/approval-commodity/{status}', ['as' => 'web-view.approval-commodity', 'uses' => 'Setting\Controllers\WebViewController@approvalCommodity']);
Route::get('pages/approval-project/{status}', ['as' => 'web-view.approval-project', 'uses' => 'Setting\Controllers\WebViewController@approvalProject']);
Route::get('pages/approval-deactivate-account/{xid}', ['as' => 'web-view.approval-deactivate-account', 'uses' => 'Setting\Controllers\WebViewController@approvalDeactivateAccount']);
