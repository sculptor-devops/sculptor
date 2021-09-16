<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Configuration
{
    private $configurations = [];

    private $filter = [
        'sculptor.services',
        'sculptor.database',
        'sculptor.php.version',
        'sculptor.monitors.disks'
    ];

    private $keys = [
        'sculptor.database.default',
        'sculptor.domains.state-machine',
        'sculptor.security.hash',
        'sculptor.security.password.min',
        'sculptor.security.password.max',
        'sculptor.monitors.rotate',
        'sculptor.backup.archive',
        'sculptor.backup.rotation',
        'sculptor.backup.temp',
        'sculptor.backup.compression',
        'sculptor.backup.drivers.default',
        'sculptor.backup.drivers.local.path',
        'sculptor.backup.drivers.s3.path',
        'sculptor.backup.drivers.s3.key',
        'sculptor.backup.drivers.s3.secret',
        'sculptor.backup.drivers.s3.region',
        'sculptor.backup.drivers.s3.endpoint',
        'sculptor.backup.drivers.s3.bucket',
        'sculptor.backup.drivers.dropbox.key'
    ];

    private $sensible = [
        'sculptor.security.key',
        'sculptor.backup.drivers.s3.key',
        'sculptor.backup.drivers.s3.secret',
        'sculptor.backup.drivers.dropbox.key'
    ];

    public function __construct()
    {
        $filename = $this->fileName();

        if (File::exists($filename)) {
            $this->configurations = Yaml::parseFile($filename);
        }
    }

    private function fileName(): string
    {
        return storage_path('app/.configuration.yml');
    }

    /**
     * @param string $name
     * @return string
     */
    private function key(string $name): string
    {
        return "config.cache.{$name}";
    }

    public function all(): array
    {
        $all = [];

        foreach ($this->keys as $key) {
            $all[$key] = $this->get($key);
        }

        return $all;
    }

    /**
     * @param string $name
     * @return string
     */
    public function get(string $name): ?string
    {
        $value = Cache::get($this->key($name), null);


        if ($value != null) {
            return $value;
        }

        $standard = config($name);

        $configuration = $this->findPointedKey($name);


        if ($name == 'sculptor.services') {
            dd($configuration);
        }



        if ($configuration != null) {
            return $configuration;
        }

        if (!is_array($standard)) {
            return $standard;
        }

        return null;
    }

    /**
     * @param string $key
     * @return string|null
     */
    private function findPointedKey(string $key): ?string
    {
        $result = $this->configurations;

        $keys = explode('.', $key);

        foreach ($keys as $key) {
            if ($key == 'sculptor') {
                continue;
            }

            if (!is_array($result)) {
                return null;
            }

            if (!array_key_exists($key, $result)) {
                return null;
            }

            $result = $result[$key];
        }

        return $result;
    }

    public function getInt(string $name): int
    {
        return intval($this->get($name));
    }

    public function getBool(string $name): int
    {
        return $this->get($name) == '1' || $this->get($name) == 'true';
    }

    public function getArray(string $name): array
    {
        return $this->get($name) ?? [];
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return $this
     * @throws Exception
     */
    public function set(string $name, ?string $value): Configuration
    {
        if (!in_array($name, $this->keys)) {
            throw new Exception("Configuration name {$name} is not valid or supported");
        }

        $this->assignArrayByPath($this->configurations, $name, $value);

        Cache::put($this->key($name), $value);

        File::put($this->fileName(), Yaml::dump($this->configurations, 10));

        return $this;
    }

    private function assignArrayByPath(array &$arr, string $path, ?string $value): void
    {
        $path = Str::replaceFirst('sculptor.', '', $path);

        $keys = explode('.', $path);

        foreach ($keys as $key) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

    /**
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function reset(string $name): Configuration
    {
        Cache::forget($this->key($name));

        $this->set($name, null);

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function clear(): Configuration
    {
        foreach ($this->keys as $name) {
            $this->reset($name);
        }

        return $this;
    }

    /**
     * @param array $connection
     */
    public function database(array $connection): void
    {
        config(['database.connections.db_server' => $connection]);
    }

    public function connection(string $driver): array
    {
        return config("sculptor.database.drivers.{$driver}");
    }

    public function php(?string $engine): string
    {
        if ($engine == null) {
            return config('sculptor.php.version');
        }

        return $engine;
    }

    public function toArray(): array
    {
        $values = [];

        $configuration = $this->recourseConfig(config('sculptor'), 'sculptor');

        foreach ($configuration as $name) {
            $values[$name] = $this->get($name);
        }

        return $filtered = collect($values)->reject(function ($value, $key) {
            return Str::startsWith($key, $this->filter);
        })->toArray();
    }

    private function recourseConfig(array $all, string $root): array
    {
        $values = [];

        foreach ($all as $key => $value) {
            if (is_array($value)) {
                $values = array_merge(
                    $values,
                    $this->recourseConfig($value, "{$root}.{$key}")
                );

                continue;
            }

            $values[] = "{$root}.{$key}";
        }

        return $values;
    }

    public function services(string $key = null): array
    {
        $values = config('sculptor.services');

        if ($key == null) {
            return $values;
        }

        return $values[$key];
    }

    public function monitors(string $key = null): array
    {
        $values = config('sculptor.monitors');

        if ($key == null) {
            return $values;
        }

        return $values[$key];
    }
}
