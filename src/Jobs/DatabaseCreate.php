<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Exceptions\DatabaseDriverException;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;

class DatabaseCreate implements ShouldQueue, ITraceable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traceable;

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
     * @return void
     * @throws Exception
     */
    public function handle(Driver $driver): void
    {
        $this->running();

        Logs::job()->info("Database create {$this->name}");

        try {
            if (!$driver->db($this->name)) {
                throw new DatabaseDriverException($driver->error());
            }

            $this->ok();
        } catch (Exception $e) {
            $this->report($e);
        }
    }
}
