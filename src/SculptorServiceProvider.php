<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
use Sculptor\Agent\Facades\Configuration as ConfigurationFacade;

class SculptorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        app()->bind(ConfigurationFacade::class, Configuration::class);

        app()->bind(Runner::class, RunnerImplementation::class);

        app()->bind(Database::class, MySql::class);

        app()->bind(Compressor::class, Zip::class);

        app()->bind(Archive::class, function () {
            $driver = ConfigurationFacade::get('sculptor.backup.drivers.default');

            switch ($driver) {
                case BackupArchiveType::LOCAL:
                    return  resolve(Local::class);

                case BackupArchiveType::S3:
                    return resolve(S3::class);

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
        $this->connection();
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
        $driver = ConfigurationFacade::get('sculptor.database.default');

        $database = ConfigurationFacade::get("sculptor.database.drivers.{$driver}");

        $database['password'] = $this->password();

        ConfigurationFacade::database(['database.connections.db_server' => $database]);
    }
}
