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

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');


Route::auth();

Route::get('ajax/{action}', 'AjaxController@index');

Route::post('ajaxPost/{action}', 'AjaxPostController@index');


Route::group(['prefix' => 'admin','middleware' => ['auth', 'locale']], function () {
	Route::get('/', 'HomeController@index');

	Route::resource('roles', 'Admin\RolesController');
	Route::resource('permissions', 'Admin\PermissionsController');
	Route::resource('users', 'Admin\UsersController');

    Route::resource('news', 'Admin\NewsController');
    Route::resource('categories', 'Admin\CategoryController');
    Route::resource('provinces', 'Admin\ProvinceController');
    Route::resource('districts', 'Admin\DistrictController');
    Route::resource('wards', 'Admin\WardController');

	Route::get('profile', 'Admin\ProfileController@getProfile');
	Route::post('profile', 'Admin\ProfileController@postProfile');
	Route::get('company-settings', 'CompanySettingsController@edit');
	Route::patch('company-settings', 'CompanySettingsController@update');
    Route::get('settings', 'SettingController@index');
	Route::patch('settings', 'SettingController@update');
	Route::post('change_locale', 'LocaleController@changeLocale');


});
