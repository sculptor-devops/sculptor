<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Database\MySql;
use Sculptor\Foundation\Runner\Runner as RunnerImplementation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	//
    }
}
