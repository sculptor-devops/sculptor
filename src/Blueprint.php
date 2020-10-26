<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\DomainRepository;
use Symfony\Component\Yaml\Yaml;

class Blueprint
{
    /**
     * @var ?array
     */
    private $commands;

    /**
     * @var Configuration
     */
    private $configuration;

    private $repositories = [
        DatabaseRepository::class => 'Databases',
        DatabaseUserRepository::class => 'DatabaseUsers',
        DomainRepository::class => 'Domains',
        BackupRepository::class => 'Backups'
    ];
    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var BackupRepository
     */
    private $backups;

    public function __construct(Configuration $configuration, DomainRepository $domains, BackupRepository $backups)
    {
        $this->configuration = $configuration;

        $this->domains = $domains;

        $this->backups = $backups;
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

        $blueprint = array_merge($blueprint, [
            'Configurations' => $this->configuration->all()
        ]);

        File::put($filename, Yaml::dump($blueprint, 999));
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

    public function load(string $filename): bool
    {
        try {
            $this->commands = [];

            $content = Yaml::parseFile($filename);

            $version = $content['header']['version'];

            if ($version != 1) {
                throw new Exception("Blueprint version must be 1, {$version} received");
            }

            foreach ($content['Databases'] as $database) {
                $this->artisan('database:create', ['name' => $database['name']]);
            }

            foreach ($content['DatabaseUsers'] as $user) {
                $this->artisan('database:user', [$user['database'], $user['name'], $user['host']]);
            }

            foreach ($content['Domains'] as $domain) {
                $this->artisan('domain:create', [$domain['name'], $domain['type']]);

                $item = $this->domains->byName($domain['name']);

                foreach ([
                             'alias',
                             'status',
                             'certificate',
                             'home',
                             'deployer',
                             'install',
                             'vcs',
                             'provider'
                         ] as $parameter) {
                    if (array_key_exists($parameter, $domain)) {
                        $this->artisan('domain:setup', [$item->name, $parameter, $domain[$parameter]]);
                    }
                }

                if ($domain['database']) {
                    $this->artisan('domain:setup', [$item->name, 'database', $domain['database']]);
                }

                if ($domain['database_user']) {
                    $this->artisan('domain:setup', [$item->name, 'user', $domain['database_user']]);
                }

                foreach ($domain['files'] as $name => $payload) {
                    $filename = "{$item->root()}/{$name}";

                    if (!$this->put($filename, $payload)) {
                        throw new Exception("Unable to write file {$filename}");
                    }
                }

                $this->artisan('domain:configure', [$item->name]);

                if (!$domain['enabled']) {
                    $this->artisan('domain:disable', [$item->name]);
                }
            }

            foreach ($content['Backups'] as $backup) {
                switch ($backup['type']) {
                    case BackupType::BLUEPRINT:
                        $this->artisan('backup:create', [$backup['type']]);
                        break;

                    case BackupType::DOMAIN:
                        $this->artisan('backup:create', [$backup['type'], $backup['domain']]);
                        break;

                    case BackupType::DATABASE:
                        $this->artisan('backup:create', [$backup['type'], $backup['database']]);
                        break;

                    default:
                        throw new Exception("Invalid backup type {$backup['type']}");
                }

                $item = $this->backups
                    ->all()
                    ->last();

                foreach (['cron', 'destination', 'rotate'] as $parameter) {
                    $this->artisan('backup:setup', [$item->id, $parameter, '"' . $backup[$parameter] . '"']);
                }
            }

            foreach ($content['Configurations'] as $name => $value) {
                $this->artisan('system:configuration', [$name, $value]);
            }

        } catch(Exception $e) {
            report($e);

            return false;
        }

        return true;
    }

    private function put(string $file, string $payload): bool
    {
        $result = true;
        // $result = File::put( $file, $payload);

        $this->pushCommand(
            'File',
            [$file],
            $result ? 'Ok' : 'Error'
        );

        return true;
    }

    private function pushCommand(string $name, array $parameters, string $result): void
    {
        $this->commands[] = [
            'id' => count($this->commands) + 1,
            'name' => $name,
            'parameters' => implode(' ', $parameters),
            'result' => $result ?? 'Ok'
        ];
    }

    private function artisan(string $name, array $parameters): void
    {
        $result = 'Ok';

        try {
            // $code = Artisan::call($name, $parameters);

            // if ($code > 0) {
            // throw new Exception("Error code {$code}");
            //}

        } catch (Exception $e) {
            report($e);

            $result = $e->getMessage();
        }

        $this->pushCommand( $name, $parameters, $result );
    }

    public function commands(): ?array
    {
        return $this->commands;
    }
}
