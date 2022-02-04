<?php

namespace Sculptor\Agent\Jobs\Database;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\Entities\DatabaseUser;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Services\Queues\Traceable;
use Sculptor\Foundation\Contracts\Database as Driver;

class UpdateUserPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var DatabaseUser
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var Database
     */
    private $db;

    /**
     * Create a new job instance.
     *
     * @param Database $db
     * @param DatabaseUser $user
     * @param string $password
     * @throws Exception
     */
    public function __construct(Database $db, DatabaseUser $user, string $password)
    {
        $this->running();

        $this->db = $db;

        $this->user = $user;

        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @param Driver $driver
     * @param DatabaseUserRepository $repository
     * @return void
     * @throws Exception
     */
    public function handle(Driver $driver, DatabaseUserRepository $repository)
    {
        try {
            if (!$driver->password($this->user->name, $this->password, $this->db->name, $this->user->host)) {
                throw new Exception($driver->error());
            }

            $repository->update(['password' => $this->password], $this->user->id);

            $this->finished();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
