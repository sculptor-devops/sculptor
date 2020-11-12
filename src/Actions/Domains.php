<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Domains\Parameters;
use Sculptor\Agent\Actions\Domains\StatusMachine;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Jobs\DomainCertbot;
use Sculptor\Agent\Jobs\DomainConfigure;
use Sculptor\Agent\Jobs\DomainCreate;
use Sculptor\Agent\Jobs\DomainCrontab;
use Sculptor\Agent\Jobs\DomainDelete;
use Sculptor\Agent\Jobs\DomainDeploy;
use Sculptor\Agent\Jobs\DomainDisable;
use Sculptor\Agent\Jobs\DomainEnable;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Jobs\DomainTemplates;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Contracts\Action as ActionInterface;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Domains implements ActionInterface
{
    use Actionable;

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
            $domain = $this->domains
                ->factory($name, $type);

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

            $domains = $this->domains
                ->deployed();

            $this->action
                ->run(new DomainDelete($domain));

            $this->action
                ->run(new DomainCrontab($domains->toArray()));

            $domain->delete();
        } catch (Exception $e) {
            return $this->action
                ->report("Delete domain {$name}: {$e->getMessage()}");
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

            $this->action
                ->run(new DaemonService(DaemonGroupType::WEB, DaemonOperationsType::RELOAD));
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

            $this->machine
                ->change($domain, DomainStatusType::DEPLOYING);

            $this->action
                ->runIndefinite(new DomainDeploy($domain, $command));
        } catch (Exception $e) {
            return $this->action
                ->report("Deploy domain {$name}: {$e->getMessage()}");
        }

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

            $this->machine
                ->change($domain, DomainStatusType::DEPLOYING);

            $this->action
                ->runAndExit(new DomainDeploy($domain, $command));
        } catch (Exception $e) {
            return $this->action
                ->report("Deploy domain {$name}: {$e->getMessage()}");
        }

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

            if (
                $this->machine
                ->can($domain->status, DomainStatusType::SETUP)
            ) {
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

            $domains = $this->domains
                ->deployed();

            $this->machine->can($domain->status, DomainStatusType::DEPLOYED);

            $this->action
                ->run(new DomainEnable($domain));

            $this->action
                ->run(new DomainCrontab($domains->toArray()));
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

            $domains = $this->domains
                ->deployed();

            $this->action
                ->run(new DomainDisable($domain));

            $this->action
                ->run(new DomainCrontab($domains->toArray()));
        } catch (Exception $e) {
            return $this->action
                ->report("Disable domain {$name}: {$e->getMessage()}");
        }

        $domain->update(['enabled' => false]);

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function templates(string $name): bool
    {
        Logs::actions()->info("Domain templates {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->run(new DomainTemplates($domain));
        } catch (Exception $e) {
            return $this->action
                ->report("Domain templates {$name}: {$e->getMessage()}");
        }

        return true;
    }

    /**
     * @param string $name
     * @param string $hook
     * @return bool
     */
    public function certbot(string $name, string $hook): bool
    {
        Logs::actions()->info("Domain certbot {$name} hook {$hook}");

        try {
            $domain = $this->domains
                ->byName($name);

            $this->action
                ->run(new DomainCertbot($domain, $hook));
        } catch (Exception $e) {
            return $this->action
                ->report("Domain certbot {$name}: {$e->getMessage()}");
        }

        return true;
    }
}
