<?php


namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Sculptor\Agent\Actions\Database;

class DeleteDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:delete {name}';

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
    public function handle(Database $actions)
    {
        $name = $this->argument('name');

        $this->info("Deleting {$name}...");

        try {
            $result = $actions->delete($name);

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
