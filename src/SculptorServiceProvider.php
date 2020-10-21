<?php

namespace Sculptor\Agent;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Database\MySql;
use Sculptor\Foundation\Runner\Runner as RunnerImplementation;

class SculptorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->connection();

        app()->bind(Runner::class, function () {
            return new RunnerImplementation();
        });

        app()->bind(Database::class, function () {
            return new MySql();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    private function password(): ?string
    {
        try {
            $password = File::get(DB_SERVER_PASSWORD);

            if (!$password) {
                return null;
            }

            return $password;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function connection(): void
    {
        $database = config('sculptor.database');

        $database['password'] = $this->password();

        config([ 'database.connections.db_server' => $database ]);
    }
}
