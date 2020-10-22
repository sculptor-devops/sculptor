<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Sculptor\Agent\Backup\Archives\Local;
use Sculptor\Agent\Backup\Archives\S3;
use Sculptor\Agent\Backup\Compression\Zip;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Enums\BackupArchiveType;
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

        app()->bind(Runner::class, RunnerImplementation::class);

        app()->bind(Database::class, MySql::class);

        app()->bind(Compressor::class, Zip::class);

        app()->bind(Archive::class, function () {
            $driver = config('sculptor.backup.drivers.default');

            switch ($driver) {
                case BackupArchiveType::LOCAL:
                    return new Local();

                case BackupArchiveType::S3:
                    return new S3();

                default:
                    throw new Exception("Invalid {$driver} archive driver");
            }
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
        } catch (Exception $e) {
            return null;
        }
    }

    private function connection(): void
    {
        $driver = config('sculptor.database.default');

        $database = config("sculptor.database.drivers.{$driver}");

        $database['password'] = $this->password();

        config(['database.connections.db_server' => $database]);
    }
}
