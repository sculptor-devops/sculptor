<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Daemons;
use Sculptor\Agent\Support\CommandBase;

class DaemonsManage extends CommandBase
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
    public function handle(Daemons $actions): int
    {
        $name = $this->argument('name');

        $operation = $this->argument('operation');

        $this->startTask("Running {$name} {$operation}");

        switch ($operation) {
            case 'enable':
                if (!$actions->enable($name)) {
                    return $this->errorTask($actions->error());
                }
                break;

            case 'disable':
                if (!$actions->disable($name)) {
                    return $this->errorTask($actions->error());
                }
                break;

            case 'restart':
                if (!$actions->restart($name)) {
                    return $this->errorTask($actions->error());
                }
                break;

            case 'reload':
                if (!$actions->reload($name)) {
                    return $this->errorTask($actions->error());
                }
                break;

            case 'start':
                if (!$actions->start($name)) {
                    return $this->errorTask($actions->error());
                }
                break;

            case 'stop':
                if (!$actions->stop($name)) {
                    return $this->errorTask($actions->error());
                }
                break;

            default:
                $daemons = collect(config('sculptor.services'))
                    ->keys()
                    ->join(', ');

                return $this->errorTask("Invalid operation {$operation}: use enable, disable, start, restart, reload, stop, status on {$daemons}");
        }

        return $this->completeTask();
    }
}
