<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Sculptor\Agent\Queues\Queues;
use Sculptor\Agent\Jobs\CreateDatabase as CreateDatabaseJob;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(Queues $queues)
    {
        $name = $this->argument('name');

        $this->info("Creating {$name}...");

        $result = $queues->await(new CreateDatabaseJob($name), 'system');

        if ($result->ok()) {
            $this->info("Done.");

            return 0;
        }

        $this->info("Error: {$result->error}");

        return 1;
    }
}
