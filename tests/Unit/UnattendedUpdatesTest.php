<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Exception;
use Tests\TestCase;
use Sculptor\Agent\Security\Upgrades;

class UnattendedUpdatesTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testEventList()
    {
	$upgrades = resolve(Upgrades::class);

	$fixture = base_path() . '/tests/Fixtures/unattended-upgrades-dpkg.log';

	$upgrades->filename($fixture);
        $this->assertCount(15, $upgrades->events());
    }
}
