<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DatabaseUserDelete extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:delete_user {database} {name} {host=localhost}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a database user';
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

        $host = $this->argument('host');

        $this->startTask("Delete user {$name}@{$host} on {$database}...");

        if (!$actions->drop($name, $database, $host)) {
            return $this->errorTask("Error: {$actions->error()}");
        }

        return $this->completeTask();
    }
}
