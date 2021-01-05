<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Lorisleiva\CronTranslator\CronParsingException;
use Lorisleiva\CronTranslator\CronTranslator;
use Sculptor\Agent\Actions\Alarms;
use Sculptor\Agent\Actions\Backups;
use Sculptor\Agent\Backup\Backup;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemSchedules extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show system schedules';

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
     * @param Backups $backups
     * @param Alarms $alarms
     * @return int
     */
    public function handle(Backups $backups, Alarms $alarms): int
    {
        $alarmSchedules = $alarms->show()->map(function ($alarm) {
            return [
                'id' => $alarm->id,
                'type' => 'Alarm',
                'name' => $alarm->name ?? 'None',
                'cron' => CronTranslator::translate($alarm->cron)
            ];
        });

        $backupSchedules = $backups->show()->map(function ($backup) {
            return [
                'id' => $backup->id,
                'type' => 'Backup',
                'name' => $backup->name(),
                'cron' => CronTranslator::translate($backup->cron)
            ];
        });

        $schedules = $alarmSchedules->merge($backupSchedules);

        $this->table(['Id', 'Type', 'Name', 'Schedule'], $schedules->toArray());

        $this->info($schedules->count() . " schedules total");

        return 0;
    }
}
