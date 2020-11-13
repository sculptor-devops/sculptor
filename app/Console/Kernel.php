<?php

namespace App\Console;

use App\Console\Commands\DaemonsManage;
use App\Console\Commands\DatabaseUserPassword;
use App\Console\Commands\DatabaseCreate;
use App\Console\Commands\DatabaseUserCreate;
use App\Console\Commands\DatabaseDelete;
use App\Console\Commands\DatabaseUserDelete;
use App\Console\Commands\SystemTasks;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\MonitorRepository;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->system($schedule);

        $this->backups($schedule);

        $this->monitors($schedule);
    }

    private function system(Schedule $schedule): void
    {
        try {
            $schedule->command('system:monitors', ['write'])->everyMinute();

            $schedule->command('system:upgrades', ['check'])->cron('59 23 * * *');

            $schedule->command('system:clear')->daily();

            // $schedule->command('queue:restart')->daily();
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

    private function monitors(Schedule $schedule): void
    {
        try {
            $monitors = resolve(MonitorRepository::class);

            foreach ($monitors->all() as $monitor) {
                $schedule->command('monitors:run', [$monitor->id])->cron($monitor->cron);
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
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
