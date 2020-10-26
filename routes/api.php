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

 Route::post('/v1/deploy/{hash}/{token}', '\Sculptor\Agent\Webhooks\Controllers\DeployDomainWebhookController@deploy')->name('v1.api.webhook.deploy');
