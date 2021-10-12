<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Sculptor\Agent\Logs\Upgrades;

class UnattendedUpdatesTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testEventList(): void
    {
        $upgrades = resolve(Upgrades::class);

        $fixture = base_path() . '/tests/Fixtures/unattended-upgrades-dpkg.log';

        $upgrades->filename($fixture);

        $events = $upgrades->events();

        $this->assertCount(5, $events);


        $this->assertEquals([
            Carbon::parse('2020-05-20 06:22:26'),
            Carbon::parse('2020-05-30 06:57:28'),
            Carbon::parse('2020-06-03 06:59:52'),
            Carbon::parse('2020-06-10 06:30:28'),
            Carbon::parse('2020-06-11 06:01:10'),
        ], $events);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testEventParse(): void
    {
        $upgrades = resolve(Upgrades::class);

        $fixture = base_path() . '/tests/Fixtures/unattended-upgrades-dpkg.log';

        $upgrades->filename($fixture);

        $events = $upgrades->events();

        $parsed = $upgrades->parse($events[0]);

        $this->assertEquals(Carbon::parse(1589955746), $parsed->start());

        $this->assertEquals(Carbon::parse(1590821853), $parsed->end());

        $this->assertCount(208, $parsed);
    }
}
