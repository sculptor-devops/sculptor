<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('login', '\Sculptor\Agent\Api\Controllers\AuthController@index')->name('login');
Route::post('/v1/login', '\Sculptor\Agent\Api\Controllers\AuthController@login')->name('v1.api.login');

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('/v1/info', '\Sculptor\Agent\Api\Controllers\InfoController@index')->name('v1.api.info');
});



