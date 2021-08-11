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

Route::get('web-view/about-us', ['as' => 'web-view.about-us', 'uses' => 'Common\WebViewController@aboutUs']);
Route::get('web-view/terms-and-condition', ['as' => 'web-view.terms-and-condition', 'uses' => 'Common\WebViewController@termsCondition']);
Route::get('web-view/privacy-policy', ['as' => 'web-view.privacy-policy', 'uses' => 'Common\WebViewController@privacyPolicy']);

Route::get('pages/about-us', ['as' => 'web-view.about-us', 'uses' => 'Common\WebViewController@aboutUs']);
Route::get('pages/terms-and-condition', ['as' => 'web-view.terms-and-condition', 'uses' => 'Common\WebViewController@termsCondition']);
Route::get('pages/privacy-policy', ['as' => 'web-view.privacy-policy', 'uses' => 'Common\WebViewController@privacyPolicy']);
