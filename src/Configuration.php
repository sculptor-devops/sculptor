<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\ConfigurationRepository;

class Configuration
{
    /**
     * @var ConfigurationRepository
     */
    private $configurations;

    private $filter = [
        'sculptor.services',
        'sculptor.database',
        'sculptor.php.version',
        'sculptor.monitors.disks'
    ];

    private $valid = [
        'sculptor.domains.state-machine',
        'sculptor.security.password.min',
        'sculptor.security.password.max',
        'sculptor.monitors.rotate',
        'sculptor.backup.archive',
        'sculptor.backup.temp',
        'sculptor.backup.compression',
        'sculptor.backup.drivers.default',
        'sculptor.backup.drivers.local.path',
        'sculptor.backup.drivers.s3.path',
        'sculptor.backup.drivers.s3.key',
        'sculptor.backup.drivers.s3.secret',
        'sculptor.backup.drivers.s3.region',
        'sculptor.backup.drivers.s3.endpoint',
        'sculptor.backup.drivers.s3.bucket'
    ];

    /**
     * Configuration constructor.
     * @param ConfigurationRepository $configurations
     */
    public function __construct(ConfigurationRepository $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @param string $name
     * @return string
     */
    private function key(string $name): string
    {
        return "config.cache.{$name}";
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

        $configuration = $this->configurations
            ->byName($name);

        if ($configuration != null) {
            return $configuration->value;
        }

        if (!is_array($standard)) {
            return $standard;
        }

        return null;
    }

    public function getInt(string $name): int
    {
        return intval($this->get($name));
    }

    public function getBool(string $name): int
    {
        return $this->get($name) == '1' || $this->get($name) == 'true';
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function set(string $name, string $value): Configuration
    {
        if (!in_array($name, $this->valid)) {
            throw new Exception("Configuration name {$name} is not valid or supported");
        }
        
        $configuration = $this->configurations->firstOrNew(['name' => $name]);

        $configuration->update(['value' => $value]);

        Cache::add($this->key($name), $value);

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function reset(string $name): Configuration
    {
        Cache::forget($this->key($name));

        $configuration = $this->configurations
            ->byName($name);

        if ($configuration != null) {
            $configuration->delete();
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function clear(): Configuration
    {
        foreach ($this->configurations->all() as $configuration) {
            $this->reset($configuration->name);
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
                $values = array_merge($values,
                    $this->recourseConfig($value, "{$root}.{$key}"));

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
