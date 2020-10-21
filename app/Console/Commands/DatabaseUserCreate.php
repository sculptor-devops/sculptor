<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\PasswordGenerator;
use Sculptor\Agent\Support\CommandBase;

class DatabaseUserCreate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:user {database} {name} {password?} {host=localhost}';

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
     * @param PasswordGenerator $passwords
     * @return int
     * @throws Exception
     */
    public function handle(Database $actions, PasswordGenerator $passwords): int
    {
        $created = false;

        $database = $this->argument('database');

        $name = $this->argument('name');

        $password = $this->argument('password');

        $host = $this->argument('host');

        $this->startTask("Creating user {$name}@{$host} on {$database}...");

        if ($password == null) {
            $created = true;

            $password = $passwords->create();
        }

        if (!$actions->user($name, $password, $database, $host)) {
            return $this->errorTask("Error: {$actions->error()}");
        }

        $this->completeTask();

        if (!$created) {
            return 0;
        }

        $this->info("Password generated: {$password}");

        return 0;
    }
}
