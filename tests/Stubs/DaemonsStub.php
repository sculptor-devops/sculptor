<?php

namespace Tests\Stubs;

use Mockery;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Agent\Actions\Daemons as Actions;

class DaemonsStub
{
    public const operations = ['start', 'stop', 'restart', 'status', 'reload', 'enable', 'disable'];

    public static function ok(): void
    {
        app()->bind(Daemons::class, function () {
            return Mockery::mock(Daemons::class, function ($mock) {
                foreach (collect(config('sculptor.services'))->flatten() as $service) {
                    foreach (DaemonsStub::operations as $action) {
                        $mock->shouldReceive($action)
                            ->with($service)
                            ->andReturnTrue();
                    }
                }
            });
        });
    }

    public static function error(): void
    {
        app()->bind(Daemons::class, function () {
            return Mockery::mock(Daemons::class, function ($mock) {
                foreach (collect(config('sculptor.services'))->flatten() as $service) {
                    foreach (DaemonsStub::operations as $action) {
                        $mock->shouldReceive($action)
                            ->with($service)
                            ->andReturnFalse();
                    }
                }
            });
        });
    }
}

