<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Jobs\DomainConfigure;
use Sculptor\Agent\Jobs\DomainCreate;
use Sculptor\Agent\Jobs\DomainDelete;
use Sculptor\Agent\Jobs\DomainDeploy;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Contracts\Action as ActionInterface;

class Domains implements ActionInterface
{
    /**
     * @const array
     */
    public const PARAMETERS = [
        'alias',
        'type',
        'certificate',
        'home',
        'deployer',
        'install',
        'vcs',
        'database',
        'user'
    ];

    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var Action
     */
    private $action;
    /**
     * @var DatabaseRepository
     */
    private $databases;
    /**
     * @var DatabaseUserRepository
     */
    private $users;

    public function __construct(
        Action $action,
        DomainRepository $domains,
        DatabaseRepository $databases,
        DatabaseUserRepository $users
    ) {
        $this->action = $action;

        $this->domains = $domains;

        $this->databases = $databases;

        $this->users = $users;
    }

    public function create(
        string $name,
        string $type = 'laravel',
        string $certificate = 'self-signed',
        string $user = 'www'
    ): bool {

        Logs::actions()->info("Create domain {$name}");

        try {
            $domain = $this->domains->firstOrCreate([
                'name' => $name,
                'type' => $type,
                'certificate' => $certificate,
                'user' => $user
            ]);

            $this->action->run(new DomainCreate($domain));

            return true;
        } catch (Exception $e) {
            $this->action->report("Create domain: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function delete(string $name): bool
    {
        Logs::actions()->info("Delete domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->run(new DomainDelete($domain));

            $domain->delete();
        } catch (Exception $e) {
            $this->action
                ->report("Deploy domain: {$e->getMessage()}");

            return false;
        }

        return true;
    }

    public function configure(string $name): bool
    {
        Logs::actions()->info("Configure domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->run(new DomainConfigure($domain));

            foreach (Daemons::SERVICES[Daemons::WEB] as $service) {
                $this->action
                    ->run(new DaemonService($service, DaemonOperationsType::RELOAD));
            }

            $domain->delete();
        } catch (Exception $e) {
            $this->action->report("Configure domain: {$e->getMessage()}");

            return false;
        }

        return true;
    }

    public function deploy(string $name, string $command = null): bool
    {
        Logs::actions()->info("Deploy domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->runIndefinite(new DomainDeploy($domain, $command));

            return true;
        } catch (Exception $e) {
            $this->action->report("Deploy domain: {$e->getMessage()}");

            return false;
        }
    }

    public function deployBatch(string $name, string $command = null): bool
    {
        Logs::actions()->info("Deploy domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->runAndExit(new DomainDeploy($domain, $command));

            return true;
        } catch (Exception $e) {
            $this->action->report("Deploy domain: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * @param string $name
     * @param string $parameter
     * @param string $value
     * @return bool
     * @throws Exception
     */
    public function setup(string $name, string $parameter, string $value): bool
    {
        Logs::actions()->info("Setup domain {$name}: {$parameter}={$value}");

        if (!in_array($parameter, Domains::PARAMETERS)) {
            throw new Exception("Invalid parameter {$parameter}");
        }

        $domain = $this->domains
            ->byName($name);

        if ($parameter == 'database') {
            $database = $this->databases
                ->byName($value);

            $domain->database()
                ->associate($database)
                ->save();

            return true;
        }

        if ($parameter == 'user') {
            $user = $this->users
                ->byName($domain->database, $value);

            $domain->databaseUser()
                ->associate($user)
                ->save();

            return true;
        }

        $domain->update(["{$parameter}" => "{$value}"]);

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function enable(string $name): bool
    {
        return $name != null;
    }

    public function disable(string $name): bool
    {
        return $name != null;
    }

    public function error(): ?string
    {
        return $this->action->error();
    }
}
