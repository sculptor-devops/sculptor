<?php


namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Sculptor\Agent\Actions\Database;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:user {database} {name} {password} {host=localhost}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database user';

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
     * @param Database $actions
     * @return int
     * @throws Exception
     */
    public function handle(Database $actions)
    {
        $database = $this->argument('database');

        $name = $this->argument('name');

        $password = $this->argument('password');

        $host = $this->argument('host');

        $this->info("Creating {$name}@{$host} on {$database}...");

        try {
            $result = $actions->user($name, $password, $database, $host);

            if ($result) {
                $this->info("Done.");

                return 0;
            }

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
        }

        return 1;
    }
}
