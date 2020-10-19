<?php


namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Database;
use Sculptor\Agent\Support\CommandBase;

class DatabaseDelete extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:delete {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a database';
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
        $name = $this->argument('name');

        $this->info("Deleting {$name}...");

        if ($actions->delete($name)) {
            $this->info("Done.");

            return 0;
        }

        $this->error("Error: {$actions->error()}");

        return 1;
    }
}
