<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Logs\Upgrades;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemUpgrades extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:upgrades {operation=list}';

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
        $operation = $this->argument('operation');

        switch ($operation) {
            case 'list':
                $this->list($logs);

                return 0;

            case 'check':
                $event = $logs->last();

                if ($event == null) {
                    return 0;
                }

                if ($event->recent()) {
                    $this->info('System upgraded recently');

                    Logs::security()->alert("System unattended upgrades " . implode(', ', $event->packages()));
                }

                return 0;
        }

        $index = intval($operation);

        if ($index > 0) {
            $this->show($logs, $index);

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
            $packages = count($logs->parse($event)
                ->packages());

            $events[] = ['index' => $index, 'upgrade' => $event->toString(), 'packages' => $packages];

            if ($event->isYesterday() || $event->isToday()) {
                $recently = true;
            }

            $index++;
        }

        $this->table(['Setting', 'Status'], [
            ['Active', $this->yesNo($logs->active())],
            ['Upgraded recently', $this->yesNo($recently)]
        ]);

        $this->table(['Index', 'Event', 'Packages'], $events);

        $this->info('Use system:upgrades <INDEX> to show complete event');
    }

    /**
     * @param Upgrades $logs
     * @param int $index
     * @throws Exception
     */
    private function show(Upgrades $logs, int $index): void
    {
        $result = [];

        $upgraded = [];

        $events = $logs->events();

        $log = $logs->parse($events[$index - 1]);

        $packages = $log->packages();

        foreach ($packages as $package) {
            $upgraded[] = [$package];
        }

        foreach ($log as $row) {
            $result[] = [$row];
        }

        $this->table([], $result);

        $this->info("Package upgraded");

        $this->table([], $upgraded);

        $this->info("Between {$log->start()} and {$log->end()}");
    }
}
