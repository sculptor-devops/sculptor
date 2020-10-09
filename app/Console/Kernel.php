<?php

namespace App\Console;

use App\Console\Commands\ChangeDatabaseUserPassword;
use App\Console\Commands\CreateDatabase;
use App\Console\Commands\CreateUser;
use App\Console\Commands\DeleteDatabase;
use App\Console\Commands\DeleteUser;
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
        CreateDatabase::class,
        DeleteDatabase::class,
        CreateUser::class,
        DeleteUser::class,
        ChangeDatabaseUserPassword::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
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
