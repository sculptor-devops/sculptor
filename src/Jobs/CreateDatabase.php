<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Queues\ITraceable;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;

class CreateDatabase implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new job instance.
     *
     * @param string $name
     * @throws Exception
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @param Driver $driver
     * @param DatabaseRepository $repository
     * @return void
     * @throws Exception
     */
    public function handle(Driver $driver, DatabaseRepository $repository)
    {
        $this->running();

        try {
            if (!$driver->db($this->name)) {
                throw new Exception($driver->error());
            }

            $repository->create([$this->name]);

            $this->finished();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
