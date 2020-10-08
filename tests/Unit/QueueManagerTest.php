<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Sculptor\Agent\Services\Queues\Manager;
use Tests\Stubs\JobOk;

class QueueManagerTest extends TestCase
{
    use DatabaseMigrations;

    function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testJobOkInsert()
    {

        Queue::fake();

	$ok = new JobOK();

        $manager = resolve(Manager::class);

        $manager->insert($ok);

	Queue::assertPushed(function (JobOK $job) use ($ok) {
            return $job->ref() === $ok->ref(); 
        });

        Queue::assertPushedOn('events', JobOK::class);
    }


    public function testJobOkWait()
    {
        $ok = new JobOK(2);

	$manager = resolve(Manager::class);

	$manager->await($ok);

	$this->assertTrue($manager->find($ok->ref()->uuid)->finished());
    }
}
