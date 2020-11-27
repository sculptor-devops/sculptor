<?php

namespace App\Providers;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [ 'App\Events\AuthLoginEventHandler@login' ],
        Attempting::class => [ 'App\Events\AuthLoginEventHandler@attempt' ],
        Lockout::class => [ 'App\Events\AuthLoginEventHandler@lockout' ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
