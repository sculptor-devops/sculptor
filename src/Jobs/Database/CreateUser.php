<?php

namespace Sculptor\Agent\Jobs\Database;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Services\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;

class CreateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

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
     * @param Database $db
     * @param string $user
     * @param string $password
     * @param string $host
     * @throws Exception
     */
    public function __construct(Database $db, string $user, string $password, string $host)
    {
        $this->running();

        $this->db = $db;

        $this->user = $user;

        $this->password = $password;

        $this->db = $db;

        $this->host = $host;
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
            if (!$driver->user($this->user, $this->password, $this->db->name, $this->host)) {
                throw new Exception($driver->error());
            }

            $repository->add($this->db, [
                'user' => $this->user,
                'password' => $this->password,
                'host' => $this->host
            ]);

            $this->finished();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
