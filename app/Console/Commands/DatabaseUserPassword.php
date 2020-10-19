<?php


namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\Support\CommandBase;

class DatabaseUserPassword extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:password {database} {name} {password} {host=localhost}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a database user password';
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

        if ($actions->password($name, $password, $database, $host)) {
            $this->info("Done.");

            return 0;
        }

        $this->error("Error: {$actions->error()}");

        return 1;
    }
}
