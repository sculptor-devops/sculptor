<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Lorisleiva\CronTranslator\CronTranslator;
use Sculptor\Agent\Actions\Monitors;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class MonitorShow extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:show {index?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable domain worker';

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
     * @param Monitors $monitors
     * @return int
     */
    public function handle(Monitors $monitors): int
    {
        $index = $this->argument('index');

        $all = collect($monitors->show());

        if ($index != null) {
            $monitor = $all->where('id', '=', $index)->first();

            $this->table([], $this->toKeyValue([
                'Id' => $monitor['id'],
                'Type' => $monitor['type'],
                'Message' => $monitor['message'],
                'To' => $monitor['to'] ?? 'None',
                'Constraint' => $monitor['constraint'] ?? 'None',
                'Cron' => CronTranslator::translate($monitor['cron']),
                'Alarm' => $this->noYes($monitor['alarm']),
                'Rearm' => $monitor['rearm'],
                'Alarm start' => $monitor['alarm_at'] ?? 'Never',
                'Last check' => $monitor['alarm_until'] ?? 'Never',
                'error' => $monitor['error'] ?? 'None'
            ]));

            return 0;
        }

        $this->table([
            'Index',
            'Type',
            'Message',
            'To',
            'Condition',
            'Cron',
            'Alarmed',
            'Rearm',
            'Error'
        ], $all->map(function($monitor){
            return [
                'id' => $monitor['id'],
                'type' => $monitor['type'],
                'message' => $monitor['message'],
                'to' => $monitor['to'] ?? 'None',
                'constraint' => $monitor['constraint'] ?? 'None',
                'cron' => CronTranslator::translate($monitor['cron']),
                'alarm' => $this->noYes($monitor['alarm']),
                'rearm' => $monitor['rearm'],
                'error' => Str::limit($monitor['error'], 30),
            ];
        })->toArray());

        return 0;
    }
}
