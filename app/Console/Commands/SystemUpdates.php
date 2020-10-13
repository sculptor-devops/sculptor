<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Sculptor\Agent\Logs\Upgrades;

class SystemUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update {verbose=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show system updates';

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
     * @param Upgrades $logs
     * @return int
     * @throws Exception
     */
    public function handle(Upgrades $logs)
    {
        $this->info("Active " . ($logs->active() ? 'YES' : 'NO'));

        dd($logs->events());


        return 0;
    }
}
