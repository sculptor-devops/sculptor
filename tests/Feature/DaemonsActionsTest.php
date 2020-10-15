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

    public function testDaemonOk():void
    {
        foreach (collect(Actions::SERVICES)->keys() as $group) {
            $this->assertTrue($this->actions->start($group), $this->actions->error() ?? 'start');

            $this->assertTrue($this->actions->stop($group), $this->actions->error() ?? 'stop');

            $this->assertTrue($this->actions->restart($group), $this->actions->error() ?? 'restart');

            $this->assertTrue($this->actions->reload($group), $this->actions->error() ?? 'reload');

            $this->assertTrue($this->actions->enable($group), $this->actions->error() ?? 'enable');

            $this->assertTrue($this->actions->disable($group), $this->actions->error() ?? 'disable');
        }
    }

    public function testDaemonError():void
    {
        DaemonsStub::error();

        foreach (collect(Actions::SERVICES)->keys() as $group) {
            $this->assertFalse($this->actions->start($group), $this->actions->error() ?? 'start');

            $this->assertFalse($this->actions->stop($group), $this->actions->error() ?? 'stop');

            $this->assertFalse($this->actions->restart($group), $this->actions->error() ?? 'restart');

            $this->assertFalse($this->actions->reload($group), $this->actions->error() ?? 'reload');

            $this->assertFalse($this->actions->enable($group), $this->actions->error() ?? 'enable');

            $this->assertFalse($this->actions->disable($group), $this->actions->error() ?? 'disable');
        }
    }
}
