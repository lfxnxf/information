<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'admin'], function () {
    Route::match(['post', 'get'], 'adminUser', ['as' => 'contact.adminUser', 'uses' => 'AdminUserController@index']);
    Route::match(['post', 'get'], 'admin', ['as' => 'contact.adminUser', 'uses' => 'AdminController@admin']);
    Route::match(['post', 'get'], 'login', ['as' => 'contact.adminUser', 'uses' => 'AdminController@login']);
});

