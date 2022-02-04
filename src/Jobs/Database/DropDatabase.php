<?php

namespace Sculptor\Agent\Jobs\Database;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Services\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;
use Sculptor\Agent\Repositories\Entities\Database;

class DropDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var Database
     */
    private $db;

    /**
     * Create a new job instance.
     *
     * @param Database $db
     * @throws Exception
     */
    public function __construct(Database $db)
    {
        $this->running();

        $this->db = $db;
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
        try {
            if (!$driver->drop($this->db->name)) {
                throw new Exception($driver->error());
            }

            $repository->delete($this->db->id);

            $this->finished();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
