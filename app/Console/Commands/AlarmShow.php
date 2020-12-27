<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Lorisleiva\CronTranslator\CronParsingException;
use Lorisleiva\CronTranslator\CronTranslator;
use Sculptor\Agent\Actions\Alarms;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class AlarmShow extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alarm:show {index?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show alarms';

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
     * @param Alarms $monitors
     * @return int
     * @throws CronParsingException
     */
    public function handle(Alarms $monitors): int
    {
        $index = $this->argument('index');

        $all = $monitors->show();

        if ($index != null) {
            $monitor = $all->where('id', '=', $index)->first();

            $this->table([], $this->toKeyValue([
                'Id' => $monitor['id'],
                'Name' => $monitor['name'] ?? 'No name',
                'Type' => $monitor['type'],
                'Message' => $monitor['message'],
                'To resource' => $monitor['to'] ?? 'None',
                'AlarmCondition' => $monitor['constraint'] ?? 'None',
                'Cron' => CronTranslator::translate($monitor['cron']),
                'AlarmAction' => $this->noYes($monitor['alarm']),
                'Rearm' => $monitor['rearm'],
                'AlarmAction start' => $monitor['alarm_at'] ?? 'Never',
                'Last check' => $monitor['alarm_until'] ?? 'Never',
                'error' => $monitor['error'] ?? 'None'
            ]));

            return 0;
        }

        $this->table([
            'Index',
            'Type',
            'Name',
            'Message',
            'To resource',
            'Conditions',
            'Cron',
            'Alarmed',
            'Rearm',
            'Error'
        ], $all->map(function($monitor){
            return [
                'id' => $monitor['id'],
                'type' => $monitor['type'],
                'name' => $monitor['name'] ?? 'No name',
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
