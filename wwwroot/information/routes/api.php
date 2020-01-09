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

Route::group(['prefix' => 'admin', 'middleware' => 'checkPermission', 'namespace' => 'Admin'], function () {
    Route::match(['post', 'get'], 'adminUser', ['as' => 'admin.adminUser', 'uses' => 'UserController@index']);
    Route::match(['post', 'get'], 'admin', ['as' => 'admin.adminUser', 'uses' => 'UserController@admin']);
});
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::match(['post', 'get'], 'login', ['as' => 'admin.adminUser', 'uses' => 'UserController@login']);
});

