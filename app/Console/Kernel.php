<?php

namespace App\Console;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\AlarmRepository;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->system($schedule);

        $this->backups($schedule);

        $this->alarms($schedule);
    }

    private function system(Schedule $schedule): void
    {
        try {
            $schedule->command('system:monitors', ['write'])->everyMinute();

            $schedule->command('system:upgrades', ['check'])->dailyAt('23:59');

            $schedule->command('system:clear')->dailyAt('00:00');

            // $schedule->command('queue:restart')->daily();

            // $schedule->command('passport:purge')->weekly();
        } catch (Exception $e) {
            Logs::batch()->report($e);
        }
    }

    private function backups(Schedule $schedule): void
    {
        try {
            $backups = resolve(BackupRepository::class);

            foreach ($backups->all() as $backup) {
                $schedule->command('backup:run', [$backup->id])->cron($backup->cron);
            }
        } catch (Exception $e) {
            Logs::batch()->report($e);
        }
    }

    private function alarms(Schedule $schedule): void
    {
        try {
            $monitors = resolve(AlarmRepository::class);

            foreach ($monitors->all() as $monitor) {
                $schedule->command('alarm:run', [$monitor->id])->cron($monitor->cron);
            }
        } catch (Exception $e) {
            Logs::batch()->report($e);
        }
    }    

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
