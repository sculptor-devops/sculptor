<?php


namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\Support\CommandBase;

class DatabaseUserCreate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:user {database} {name} {password} {host=localhost}';

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
    public function handle(Database $actions): int
    {
        $database = $this->argument('database');

        $name = $this->argument('name');

        $password = $this->argument('password');

        $host = $this->argument('host');

        $this->startTask("Creating user {$name}@{$host} on {$database}...");

        if (!$actions->user($name, $password, $database, $host)) {
            return $this->errorTask("Error: {$actions->error()}");
        }

        return $this->completeTask();
    }
}
