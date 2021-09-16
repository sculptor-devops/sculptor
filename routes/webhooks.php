<?php

use Illuminate\Support\Facades\Route;

Route::post('/v1/deploy/{hash}/{token}', '\Sculptor\Agent\Webhooks\Controllers\DeployDomainWebhookController@deploy')->name('v1.webhook.deploy');
