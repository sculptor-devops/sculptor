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
     */
    public function set(string $name, string $value): Configuration
    {
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
