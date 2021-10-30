<?php

namespace Sculptor\Agent;

use Exception;
use Sculptor\Agent\Support\CommandHide;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Sculptor\Agent\Backup\Compression\Zip;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Backup\Contracts\Rotation;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Database\MySql;
use Sculptor\Foundation\Runner\Runner as RunnerImplementation;
use Sculptor\Agent\Facades\Configuration as ConfigurationFacade;
use Sculptor\Agent\Facades\Logs as LogsFacade;
use Sculptor\Agent\Support\PhpVersions;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Backup\Factory;
use Sculptor\Agent\Monitors\System;
use Sculptor\Agent\LookupResolver;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SculptorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ConfigurationFacade::class, Configuration::class);

        $this->app->bind(LogsFacade::class, Logs::class);

        $this->app->bind(Runner::class, RunnerImplementation::class);

        $this->app->bind(Database::class, MySql::class);

        $this->app->bind(Compressor::class, Zip::class);

        $this->app->bind(Rotation::class, fn($app) => LookupResolver::driver($app, 'sculptor.backup.rotations', 'sculptor.backup.rotation'));

        $this->app->bind(Archive::class, fn($app) => LookupResolver::driver($app, 'sculptor.backup.drivers.available', 'sculptor.backup.drivers.default'));

        $this->app->when(Factory::class)
            ->needs('$lookup')
            ->give((fn($app) => LookupResolver::array($app, 'sculptor.backup.strategies')));

        $this->app->when(System::class)
            ->needs('$monitors')
            ->give((fn($app) => LookupResolver::array($app, 'sculptor.monitors.drivers')));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->versions();

        $this->connection();

        $this->validation();

        CommandHide::hide();        
    }

    private function validation(): void
    {
        Validator::extend('fqdn', 'App\Rules\Fqdn');

        Validator::extend('vcs', 'App\Rules\Vcs');

        Validator::extend('resolvable_condition', 'App\Rules\ResolvableCondition');

        Validator::extend('resolvable_rotation', 'App\Rules\ResolvableRotation');

        Validator::extend('resolvable', 'App\Rules\Resolvable');

        Validator::extend('cron', 'App\Rules\Cron');
    }

    private function connection(): void
    {
        $driver = ConfigurationFacade::get('sculptor.database.default');

        $database = config("sculptor.database.drivers.{$driver}");

        $database['password'] = ConfigurationFacade::password();

        ConfigurationFacade::database($database);
    }

    private function versions(): void
    {
        $key = 'sculptor.services.' . DaemonGroupType::WEB;

        $versions = $this->app->get(PhpVersions::class);

        $services = config($key);

        foreach ($versions->available() as $version) {
            $name = "php{$version}-fpm";

            if (!in_array($name, $services)) {
                $services[] = $name;
            }
        }

        config([$key => $services]);
    }
}
