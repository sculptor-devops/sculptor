<?php


namespace App\Console\Commands;


use Exception;
use Sculptor\Agent\Logs\Upgrades;
use Sculptor\Agent\Monitors\Collector;
use Sculptor\Agent\Support\CommandBase;

class SystemMonitors extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:monitors {operation=show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage system monitors';

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
     * @param Collector $monitors
     * @return int
     * @throws Exception
     */
    public function handle(Collector $monitors): int
    {
        $operation = $this->argument('operation');

        $this->startTask("Monitors {$operation}");

        switch ($operation) {
            case 'reset':
                $monitors->reset();;

                return $this->completeTask();

            case 'write':
                $monitors->write();;

                return $this->completeTask();

            case 'show':
                $this->completeTask();

                $values = $monitors->last();

                return 0;
        }

        $this->error("Unknown operation {$operation}");

        return 1;
    }
}
