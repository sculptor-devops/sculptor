<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\PasswordGenerator;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DatabaseUserPassword extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:password {database} {name} {host=localhost} {password?}';

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
     * @param PasswordGenerator $passwords
     * @return int
     * @throws Exception
     */
    public function handle(Database $actions, PasswordGenerator $passwords): int
    {
        $database = $this->argument('database');

        $name = $this->argument('name');

        $password = $this->argument('password');

        $host = $this->argument('host');

        $this->startTask("Updating password {$name}@{$host} on {$database}...");

        if ($password == null) {
            $password = $passwords->create();
        }

        if (!$actions->password($name, $password, $database, $host)) {
            return $this->errorTask("Error: {$actions->error()}");
        }

        return $this->completeTask();
    }
}
