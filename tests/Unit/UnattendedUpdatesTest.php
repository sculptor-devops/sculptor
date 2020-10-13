<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Carbon\Carbon;
use Exception;
use Tests\TestCase;
use Sculptor\Agent\Logs\Upgrades;

class UnattendedUpdatesTest extends TestCase
{
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

        $this->assertCount(15, $events);

        $this->assertEquals([
            Carbon::parse('2020-05-19 06:57:45'),
            Carbon::parse('2020-05-19 06:57:50'),
            Carbon::parse('2020-05-20 06:22:26'),
            Carbon::parse('2020-05-20 06:22:32'),
            Carbon::parse('2020-05-20 06:22:37'),
            Carbon::parse('2020-05-20 06:23:25'),
            Carbon::parse('2020-05-30 06:57:28'),
            Carbon::parse('2020-06-03 06:59:52'),
            Carbon::parse('2020-06-10 06:30:28'),
            Carbon::parse('2020-06-10 06:31:06'),
            Carbon::parse('2020-06-10 06:31:11'),
            Carbon::parse('2020-06-11 06:01:10'),
            Carbon::parse('2020-06-11 06:01:18'),
            Carbon::parse('2020-06-11 06:01:33'),
            Carbon::parse('2020-06-11 06:02:12')
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

        $this->assertEquals(Carbon::parse(1589871465), $parsed->start());

        $this->assertEquals(Carbon::parse(1591855337), $parsed->end());

        $this->assertCount(592, $parsed);
    }
}
