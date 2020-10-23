<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\Cache;
use Sculptor\Agent\Repositories\ConfigurationRepository;

class Configuration
{
    /**
     * @var ConfigurationRepository
     */
    private $configurations;

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
     * @return mixed
     */
    public function get(string $name)
    {
        $value = Cache::get($this->key($name), null);

        if ($value != null) {
            return json_decode($value);
        }

        $configuration = $this->configurations
            ->byName($name);

        if ($configuration == null) {
            return config($name);
        }

        return json_decode($configuration->value, true);
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function set(string $name, string $value): Configuration
    {
        $encoded = json_encode($value);

        $configuration = $this->configurations->firstOrNew(['name' => $name]);

        $configuration->update(['value' => $encoded]);

        Cache::add($this->key($name), $encoded);

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
}
