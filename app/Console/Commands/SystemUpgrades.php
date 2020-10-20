<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Logs\Upgrades;
use Sculptor\Agent\Support\CommandBase;

class SystemUpgrades extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:upgrades {show=list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show system upgrades';
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
    public function handle(Upgrades $logs): int
    {
        $show = $this->argument('show');

        if ($show == 'list') {
            $this->list($logs);

            return 0;
        }

        if (intval($show) > 0) {
            $this->show($logs, intval($show));

            return 0;
        }


        return 1;
    }

    /**
     * @param Upgrades $logs
     * @throws Exception
     */
    private function list(Upgrades $logs): void
    {
        $index = 1;

        $events = [];

        $recently = false;

        foreach ($logs->events() as $event) {
            $events[] = [ 'index' => $index, 'upgrade' => $event->toString() ];

            if ($event->isYesterday() || $event->isToday()) {
                $recently = true;
            }

            $index++;
        }

        $this->table(['Setting', 'Status'], [
            [ 'Active', $this->yesNo($logs->active() ) ],
            [ 'Upgraded recently', $this->yesNo($recently) ]
        ]);

        $this->table(['Index', 'Event'], $events);

        $this->info('Use system:upgrades <INDEX> to show complete event');
    }

    /**
     * @param Upgrades $logs
     * @param int $index
     * @throws Exception
     */
    private function show(Upgrades $logs, int $index): void
    {
        $events = $logs->events();

        $log = $logs->parse($events[$index - 1]);

        $result = [];

        foreach ($log as $row) {
            $result[] = [ $row ];
        }

        $this->table([], $result);

        $this->info("Between {$log->start()} and {$log->end()}");
    }
}
