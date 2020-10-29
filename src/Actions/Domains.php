<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Domains\Parameters;
use Sculptor\Agent\Actions\Domains\StatusMachine;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Report;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Jobs\DomainConfigure;
use Sculptor\Agent\Jobs\DomainCreate;
use Sculptor\Agent\Jobs\DomainDelete;
use Sculptor\Agent\Jobs\DomainDeploy;
use Sculptor\Agent\Jobs\DomainDisable;
use Sculptor\Agent\Jobs\DomainEnable;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Contracts\Action as ActionInterface;

class Domains implements ActionInterface
{
    use Report;

    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var StatusMachine
     */
    private $machine;
    /**
     * @var Parameters
     */
    private $parameters;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Configuration $configuration,
        Action $action,
        StatusMachine $machine,
        DomainRepository $domains,
        Parameters $parameters
    ) {
        $this->configuration = $configuration;

        $this->action = $action;

        $this->machine = $machine;

        $this->domains = $domains;

        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     * @param string $type
     * @return bool
     */
    public function create(
        string $name,
        string $type = 'laravel'
    ): bool {

        Logs::actions()->info("Create domain {$name}");

        try {
            $domain = $this->domains->factory($name, $type);

            $this->action
                ->run(new DomainCreate($domain));
        } catch (Exception $e) {
            return $this->action
                ->report("Create domain {$name}: {$e->getMessage()}");
        }

        return true;
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
            return $this->action
                ->report("Deploy domain {$name}: {$e->getMessage()}");
        }

        return true;
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function configure(string $name): bool
    {
        Logs::actions()->info("Configure domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->run(new DomainConfigure($domain));

            foreach ($this->configuration->services(DaemonGroupType::WEB) as $service) {
                $this->action
                    ->run(new DaemonService($service, DaemonOperationsType::RELOAD));
            }
        } catch (Exception $e) {
            return $this->action
                ->report("Configure domain {$name}: {$e->getMessage()}");
        }

        $this->machine
            ->change($domain, DomainStatusType::CONFIGURED);

        return true;
    }

    /**
     * @param string $name
     * @param string|null $command
     * @return bool
     * @throws Exception
     */
    public function deploy(string $name, string $command = null): bool
    {
        Logs::actions()->info("Deploy domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->runIndefinite(new DomainDeploy($domain, $command));
        } catch (Exception $e) {
            return $this->action
                ->report("Deploy domain {$name}: {$e->getMessage()}");
        }

        $this->machine
            ->change($domain, DomainStatusType::DEPLOYED);

        return true;
    }

    /**
     * @param string $name
     * @param string|null $command
     * @return bool
     * @throws Exception
     */
    public function deployBatch(string $name, string $command = null): bool
    {
        Logs::actions()->info("Deploy domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->runAndExit(new DomainDeploy($domain, $command));
        } catch (Exception $e) {
            return $this->action
                ->report("Deploy domain {$name}: {$e->getMessage()}");
        }

        $this->machine
            ->change($domain, DomainStatusType::DEPLOYED);

        return true;
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

        try {
            $domain = $this->domains
                ->byName($name);

            if ($this->machine
                ->can($domain->status, DomainStatusType::SETUP)) {
                $this->parameters
                    ->set($domain, $parameter, $value);
            }
        } catch (Exception $e) {
            return $this->action
                ->report("Enable domain {$name}: {$e->getMessage()}");
        }

        $this->machine
            ->change($domain, DomainStatusType::SETUP);

        return true;
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function enable(string $name): bool
    {
        Logs::actions()->info("Enable domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->machine->can($domain->status, DomainStatusType::DEPLOYED);

            $this->action
                ->run(new DomainEnable($domain));
        } catch (Exception $e) {
            return $this->action
                ->report("Enable domain {$name}: {$e->getMessage()}");
        }

        $domain->update(['enabled' => true]);

        return true;
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function disable(string $name): bool
    {
        Logs::actions()->info("Disable domain {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->run(new DomainDisable($domain));
        } catch (Exception $e) {
            return $this->action
                ->report("Disable domain {$name}: {$e->getMessage()}");
        }

        $domain->update(['enabled' => false]);

        return true;
    }
}
