<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Daemons;
use Sculptor\Agent\Support\CommandBase;

class SystemDaemons extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:daemons {operation=show} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daemons status and operations';
    /**
     * @var Daemons
     */
    private $actions;

    /**
     * Create a new command instance.
     *
     * @param Daemons $actions
     */
    public function __construct(Daemons $actions)
    {
        parent::__construct();

        $this->actions = $actions;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $operation = $this->argument('operation');

        $name = $this->argument('name');

        $this->startTask("Running {$name} {$operation}");

        if ($operation == 'show') {
            $this->completeTask();

            $this->show();

            return 0;
        }

        if (in_array($operation, [
            'enable',
            'disable',
            'restart',
            'reload',
            'start',
            'stop'
        ])) {
            if (!$this->actions->$operation($name)) {
                return $this->errorTask($this->actions->error());
            }

            return $this->completeTask();
        }

        $daemons = collect(config('sculptor.services'))
            ->keys()
            ->join(', ');

        return $this->errorTask("Invalid operation {$operation}: use enable, disable, start, restart, reload, stop, status on {$daemons}");
    }

    private function show(): void
    {
        $tabled = collect($this->actions->status())
            ->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'group' => Str::upper($item['group']),
                    'running' => $this->yesNo($item['active'])
                ];
            });

        $this->table(['Service', 'Group', 'Running'], $tabled);
    }
}
