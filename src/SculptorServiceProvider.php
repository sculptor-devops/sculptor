<?php

namespace Sculptor\Agent;

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
    public function register()
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
    public function boot()
    {
        //
    }

    private function password(): ?string
    {
        try {
            return file_get_contents(DB_SERVER_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function connection(): void
    {
        config([
            'database.connections.db_server' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'mysql',
                'username' => 'root',
                'password' => $this->password()
            ]
        ]);
    }
}
