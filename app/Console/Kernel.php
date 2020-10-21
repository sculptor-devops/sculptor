<?php

namespace App\Console;

use App\Console\Commands\DaemonsManage;
use App\Console\Commands\DatabaseUserPassword;
use App\Console\Commands\DatabaseCreate;
use App\Console\Commands\DatabaseUserCreate;
use App\Console\Commands\DatabaseDelete;
use App\Console\Commands\DatabaseUserDelete;
use App\Console\Commands\QueueTasksStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
         $schedule->command('system:monitors', [ 'write' ])->everyMinute();

         // $schedule->command('queue:restart', [ 'write' ])->daily();
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
