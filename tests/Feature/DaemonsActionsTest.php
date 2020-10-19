<?php

namespace Tests\Feature;

use Sculptor\Agent\Actions\Daemons as Actions;
use Tests\Stubs\DaemonsStub;
use Tests\TestCase;

class DaemonsActionsTest extends TestCase
{
    /**
     * @var Actions|null
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
        foreach (collect(config('sculptor.services'))->keys() as $group) {
            $this->assertTrue($this->actions->start($group), $this->actions->error() ?? "error start {$group}");

            $this->assertTrue($this->actions->stop($group), $this->actions->error() ?? "error stop {$group}");

            $this->assertTrue($this->actions->restart($group), $this->actions->error() ?? "error restart {$group}");

            $this->assertTrue($this->actions->reload($group), $this->actions->error() ?? "error reload {$group}");

            $this->assertTrue($this->actions->enable($group), $this->actions->error() ??  "error enable {$group}");

            $this->assertTrue($this->actions->disable($group), $this->actions->error() ?? "error disable {$group}");
        }
    }

    public function testDaemonError():void
    {
        DaemonsStub::error();

        foreach (collect(config('sculptor.services'))->except(['database'])->keys() as $group) {

            $this->assertFalse($this->actions->start($group), $this->actions->error() ?? "error start {$group}");

            $this->assertFalse($this->actions->stop($group), $this->actions->error() ?? "error stop {$group}");

            $this->assertFalse($this->actions->restart($group), $this->actions->error() ?? "error restart {$group}");

            $this->assertFalse($this->actions->reload($group), $this->actions->error() ?? "error reload {$group}");

            $this->assertFalse($this->actions->enable($group), $this->actions->error() ?? "error enable {$group}");

            $this->assertFalse($this->actions->disable($group), $this->actions->error() ?? "error disable {$group}");
        }
    }
}
