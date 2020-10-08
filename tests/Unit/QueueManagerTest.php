<?php

namespace Tests\Unit;

use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Database\MySql;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Sculptor\Agent\Queues\Queues;
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

        $manager = resolve(Queues::class);

        $manager->insert($ok);

        Queue::assertPushed(function (JobOK $job) use ($ok) {
            return $job->ref() === $ok->ref();
        });

        Queue::assertPushedOn('events', JobOK::class);
    }


    public function testJobOkWait()
    {
        $ok = new JobOK(1);

        $manager = resolve(Queues::class);

        $manager->await($ok);

        $this->assertTrue($manager->find($ok->ref()->uuid)->finished());
    }
}
