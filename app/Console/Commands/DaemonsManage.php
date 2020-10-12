<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sculptor\Agent\Actions\Daemons;

class DaemonsManage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemons:manage {operation} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage daemons';

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
     * @param Daemons $actions
     * @return int
     */
    public function handle(Daemons $actions)
    {
        $name = $this->argument('name');

        $operation = $this->argument('operation');

        switch ($operation) {
            case 'enable':
                if (!$actions->enable($name)) {
                    $this->error("Error: {$actions->error()}");

                    return 2;
                }
                break;

            case 'disable':
                if (!$actions->disable($name)) {
                    $this->error("Error: {$actions->error()}");

                    return 2;
                }
                break;

            case 'restart':
                if (!$actions->restart($name)) {
                    $this->error("Error: {$actions->error()}");

                    return 2;
                }
                break;

            case 'reload':
                if (!$actions->reload($name)) {
                    $this->error("Error: {$actions->error()}");

                    return 2;
                }
                break;

            case 'start':
                if (!$actions->start($name)) {
                    $this->error("Error: {$actions->error()}");

                    return 2;
                }
                break;

            case 'stop':
                if (!$actions->stop($name)) {
                    $this->error("Error: {$actions->error()}");

                    return 2;
                }
                break;

            default:
                $daemons = collect(Daemons::SERVICES)->keys()->join(', ');

                $this->error("Invalid operation {$operation}: use enable, disable, start, restart, reload, stop, status on {$daemons}");

                return 1;
        }

        $this->info("Done.");

        return 0;
    }
}
