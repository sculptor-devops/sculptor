<?php

namespace Tests\Feature;

use Mockery;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Agent\Actions\Daemons as Actions;
use Tests\Stubs\DaemonsStub;
use Tests\TestCase;

class DaemonsActionsTest extends TestCase
{
    /**
     * @var Daemons|null
     */
    private $actions;

    function setUp(): void
    {
        parent::setUp();

        DaemonsStub::ok();

        $this->actions = resolve(Actions::class);
    }

    public function testDaemonStart():void
    {
        // DaemonsStub::error();

        dd( $this->actions->start('web'));

    }
}
