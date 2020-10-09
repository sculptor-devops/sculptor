<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Queues\ITraceable;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;

class DatabaseUserCreate implements ShouldQueue, ITraceable
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
    private $host;
    /**
     * @var string
     */
    private $db;

    /**
     * Create a new job instance.
     *
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string $host
     */
    public function __construct(string $user, string $password, string $db, string $host)
    {
        $this->user = $user;

        $this->password = $password;

        $this->db = $db;

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
            if (!$driver->user($this->user, $this->password, $this->db, $this->host)) {
                throw new Exception($driver->error());
            }

            $this->finished();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
