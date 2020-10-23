<?php

namespace Sculptor\Agent;

use Illuminate\Support\Facades\File;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\ConfigurationRepository;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\DomainRepository;
use Symfony\Component\Yaml\Yaml;

class Blueprint
{
    /**
     * @var Configuration
     */
    private $configuration;

    private $repositories = [
        DatabaseRepository::class => 'Databases',
        DatabaseUserRepository::class => 'DatabaseUsers',
        DomainRepository::class => 'Domains',
        BackupRepository::class => 'Backups',
        ConfigurationRepository::class => 'Configurations'
    ];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function create(string $filename): void
    {
        $blueprint = ['header' => ['version' => 1, 'timestamp' => now()]];

        foreach ($this->repositories as $class => $name) {

            $values = $this->serialize($class);

            if (count($values) == 0) {
                $values = null;
            }

            $blueprint[$name] = $values;
        }

        File::put($filename, Yaml::dump($blueprint, 3));
    }

    private function serialize(string $class): array
    {
        $values = [];

        $repository = resolve($class);

        foreach ($repository->all() as $record) {
            $values[] = $record->serialize();
        }

        return $values;
    }
}
