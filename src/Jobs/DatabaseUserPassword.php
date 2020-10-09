<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Exceptions\DatabaseDriverException;
use Sculptor\Agent\Queues\ITraceable;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;

class DatabaseUserPassword implements ShouldQueue, ITraceable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traceable;

    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $db;
    /**
     * @var string
     */
    private $host;

    /**
     * Create a new job instance.
     *
     * @param string $user
     * @param string $db
     * @param string $password
     * @param string $host
     */
    public function __construct(string $user, string $db, string $password, string $host)
    {
        $this->user = $user;

        $this->db = $db;

        $this->password = $password;

        $this->host = $host;
    }

    /**
     * Execute the job.
     *
     * @param Driver $driver
     * @return void
     * @throws Exception
     */
    public function handle(Driver $driver)
    {
        $this->running();

        try {
            if (!$driver->password($this->user, $this->password, $this->db, $this->host)) {
                throw new DatabaseDriverException($driver->error());
            }

            $this->finished();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
