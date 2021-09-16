<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Sculptor\Agent\Backup\Archives\Dropbox;
use Sculptor\Agent\Backup\Archives\Local;
use Sculptor\Agent\Backup\Archives\S3;
use Sculptor\Agent\Backup\Compression\Zip;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Enums\BackupArchiveType;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Database\MySql;
use Sculptor\Foundation\Runner\Runner as RunnerImplementation;
use Sculptor\Agent\Facades\Configuration as ConfigurationFacade;
use Sculptor\Agent\Facades\Logs as LogsFacade;
use Sculptor\Agent\Support\PhpVersions;
use Sculptor\Agent\Enums\DaemonGroupType;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SculptorServiceProvider extends ServiceProvider
{
    private $hidden = [
        'vendor:publish',
        'inspire',
        'serve',
        'tinker',
        'db:seed',
        'db:wipe',
        'make:bindings',
        'make:cast',
        'make:channel',
        'make:command',
        'make:component',
        'make:controller',
        'make:criteria',
        'make:entity',
        'make:event',
        'make:exception',
        'make:factory',
        'make:job',
        'make:listener',
        'make:mail',
        'make:middleware',
        'make:migration',
        'make:model',
        'make:notification',
        'make:observer',
        'make:policy',
        'make:presenter',
        'make:provider',
        'make:repository',
        'make:request',
        'make:resource',
        'make:rest-controller',
        'make:rule',
        'make:seeder',
        'make:test',
        'make:transformer',
        'make:validator',
        'notifications:table',
        'schema:dump',
        'session:table',
        'storage:link',
        'stub:publish',
        'cache:table',
        'queue:batches-table',
        'queue:failed-table',
        'queue:table'
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        app()->bind(ConfigurationFacade::class, Configuration::class);

        app()->bind(LogsFacade::class, Logs::class);

        app()->bind(Runner::class, RunnerImplementation::class);

        app()->bind(Database::class, function () {
            return new MySql();
        });

        app()->bind(Compressor::class, Zip::class);

        app()->bind(Archive::class, function () {
            $driver = ConfigurationFacade::get('sculptor.backup.drivers.default');

            switch ($driver) {
                case BackupArchiveType::LOCAL:
                    return resolve(Local::class);

                case BackupArchiveType::S3:
                    return resolve(S3::class);

                case BackupArchiveType::DROPBOX:
                    return resolve(Dropbox::class);

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
        $this->versions();

        $this->hideCommands();

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

        $database = config("sculptor.database.drivers.{$driver}");

        $database['password'] = $this->password();

        ConfigurationFacade::database($database);
    }

    private function hideCommands(): void
    {
        if (!App::environment('production')) {
            return;
        }

        foreach (Artisan::all() as $key => $command) {
            if (in_array($key, $this->hidden)) {
                $command->setHidden(true);
            }
        }
    }

    private function versions(): void
    {
        $key = 'sculptor.services.' . DaemonGroupType::WEB;

        $versions = resolve(PhpVersions::class);

        $services = config($key);

        foreach ($versions->available() as $version) {
            $name = "php{$version}-fpm";

            if (!in_array($name, $services)) {
                $services[] = $name;
            }
        }

        config([ $key => $services ]);
    }
}
