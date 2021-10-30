<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Repositories\AlarmRepository;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Repositories\UserRepository;
use Symfony\Component\Yaml\Yaml;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Blueprint
{
    /**
     * @var array
     */
    private $parameters = [
        'alias',
        'status',
        'certificate',
        'home',
        'deployer',
        'install',
        'vcs',
        'provider',
        'branch'
    ];

    /**
     * @var ?array
     */
    private $commands;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $error;

    /**
     * @var string[]
     */
    private $repositories = [
        DatabaseRepository::class => 'Databases',
        DatabaseUserRepository::class => 'DatabaseUsers',
        DomainRepository::class => 'Domains',
        BackupRepository::class => 'Backups',
        AlarmRepository::class => 'Alarms',
        UserRepository::class => 'Users',
    ];

    /**
     * @var
     */
    private $content;

    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var BackupRepository
     */
    private $backups;
    /**
     * @var AlarmRepository
     */
    private $alarms;

    /**
     * @var bool
     */
    private $dry = false;

    /**
     * Blueprint constructor.
     * @param Configuration $configuration
     * @param DomainRepository $domains
     * @param BackupRepository $backups
     * @param AlarmRepository $alarms
     */
    public function __construct(
        Configuration $configuration,
        DomainRepository $domains,
        BackupRepository $backups,
        AlarmRepository $alarms
    ) {
        $this->configuration = $configuration;

        $this->domains = $domains;

        $this->backups = $backups;

        $this->alarms = $alarms;
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function create(string $filename): bool
    {
        try {
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

            if (!File::put($filename, Yaml::dump($blueprint, 999))) {
                throw new Exception("Unable to write {$filename}");
            }
        } catch (Exception $e) {
            report($e);

            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function load(string $filename): bool
    {
        try {
            $this->commands = [];

            $this->content = Yaml::parseFile($filename);

            $this->headers();

            $this->configurations();

            $this->databases();

            $this->databaseUsers();

            $this->domains();

            $this->backups();

            $this->alarms();

            $this->users();
        } catch (Exception $e) {
            report($e);

            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @return array|null
     */
    public function commands(): ?array
    {
        return $this->commands;
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * @param string $class
     * @return array
     */
    private function serialize(string $class): array
    {
        $values = [];

        $repository = resolve($class);

        foreach ($repository->all() as $record) {
            $values[] = $record->serialize();
        }

        return $values;
    }

    /**
     * @throws Exception
     */
    private function headers(): void
    {
        $version = $this->content['header']['version'];

        if ($version != BLUEPRINT_VERSION) {
            throw new Exception("Blueprint version must be 1, {$version} received");
        }
    }

    /**
     *
     */
    private function databases(): void
    {
        if (!$this->content['Databases']) {
            return;
        }

        foreach ($this->content['Databases'] as $database) {
            $this->artisan('database:create', ['name' => $database['name']]);
        }
    }

    /**
     *
     */
    private function databaseUsers(): void
    {
        if (!$this->content['DatabaseUsers']) {
            return;
        }

        foreach ($this->content['DatabaseUsers'] as $user) {
            $this->artisan('database:user', [$user['database'], $user['name'], $user['host']]);
        }
    }

    /**
     *
     */
    private function users(): void
    {
        if (!$this->content['Users']) {
            return;
        }

        foreach ($this->content['Users'] as $user) {
            $this->artisan('system:user', ['create', $user['name'], $user['email']]);
        }
    }

    /**
     *
     * @throws Exception
     */
    private function alarms(): void
    {
        if (!$this->content['Alarms']) {
            return;
        }

        foreach ($this->content['Alarms'] as $alarm) {
            $this->artisan('alarm:create', [$alarm['type']]);

            $item = $this->alarms->last();

            foreach (['message', 'to', 'name', 'monitor', 'condition', 'rearm'] as $key) {
                $this->artisan('alarm:setup', [$item->id, $key, $alarm[$key]]);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function domains(): void
    {
        if (!$this->content['Domains']) {
            return;
        }

        foreach ($this->content['Domains'] as $domain) {
            $this->artisan('domain:create', [$domain['name'], $domain['type']]);

            $item = $this->domains->byName($domain['name']);

            foreach ($this->parameters as $parameter) {
                if (array_key_exists($parameter, $domain)) {
                    $this->artisan('domain:setup', [$item->name, $parameter, $domain[$parameter]]);
                }
            }

            if ($domain['database'] != null) {
                $this->artisan('domain:setup', [$item->name, 'database', $domain['database']]);
            }

            if ($domain['database_user'] != null) {
                $this->artisan('domain:setup', [$item->name, 'user', $domain['database_user']]);
            }

            foreach ($domain['files'] as $name => $payload) {
                $filename = "{$item->root()}/{$name}";

                if (!$this->file($filename, $payload)) {
                    throw new Exception("Unable to write file {$filename}");
                }
            }

            $this->artisan('domain:configure', [$item->name]);

            if (!$domain['enabled']) {
                $this->artisan('domain:disable', [$item->name]);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function backups(): void
    {
        if (!$this->content['Backups']) {
            return;
        }

        foreach ($this->content['Backups'] as $backup) {
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
    }

    /**
     *
     */
    private function configurations(): void
    {
        if (!$this->content['Configurations']) {
            return;
        }

        foreach ($this->content['Configurations'] as $name => $value) {
            $this->artisan('system:configuration', [$name, $value]);
        }
    }

    /**
     * @param string $file
     * @param string $payload
     * @return bool
     */
    private function file(string $file, string $payload): bool
    {
        if ($this->dry) {
            $this->pushCommand(
                'File',
                [$file],
                'dry run'
            );

            return true;
        }

        try {
            $result = File::put($file, $payload);

            if (!$result) {
                throw new Exception("Error writing {$file}");
            }
        } catch (Exception $e) {
            report($e);

            $result = $e->getMessage();
        }

        $this->pushCommand(
            'File',
            [$file],
            $result
        );

        return $result;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param string $result
     */
    private function pushCommand(string $name, array $parameters, string $result): void
    {
        $this->commands[] = [
            'id' => count($this->commands) + 1,
            'name' => $name,
            'parameters' => '"'  . implode('" "', $parameters) . '"',
            'result' => $result ?? 'Ok'
        ];
    }

    /**
     * @param string $name
     * @param array $parameters
     */
    private function artisan(string $name, array $parameters): void
    {
        $result = 'Ok';

        if ($this->dry) {
            $this->pushCommand($name, $parameters, 'dry run');

            return;
        }

        try {
            $code = Artisan::call($name, $parameters);

            if ($code > 0) {
                throw new Exception("Error code {$code}");
            }
        } catch (Exception $e) {
            report($e);

            $result = $e->getMessage();
        }

        $this->pushCommand($name, $parameters, $result);
    }

    public function dry(): void
    {
        $this->dry = true;
    }
}
