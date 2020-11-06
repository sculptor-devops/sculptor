<?php

namespace App\Console;

use App\Console\Commands\DaemonsManage;
use App\Console\Commands\DatabaseUserPassword;
use App\Console\Commands\DatabaseCreate;
use App\Console\Commands\DatabaseUserCreate;
use App\Console\Commands\DatabaseDelete;
use App\Console\Commands\DatabaseUserDelete;
use App\Console\Commands\SystemTasks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Sculptor\Agent\Repositories\BackupRepository;

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
        $backups = resolve(BackupRepository::class);

        $schedule->command('system:monitors', [ 'write' ])->everyMinute();

        $schedule->command('system:upgrades', [ 'check' ])->cron('59 23 * * *');

        $schedule->command('system:clear' )->daily();

        foreach ($backups->all() as $backup) {
            $schedule->command('backup:run', [ $backup->id ])->cron($backup->cron);
        }

        // $schedule->command('queue:restart', [ 'write' ])->daily();
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
