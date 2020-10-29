<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Report;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Jobs\DomainWorkerDisable;
use Sculptor\Agent\Jobs\DomainWorkerEnable;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\DomainRepository;

class Workers implements ActionInterface
{
    use Report;

    /**
     * @var DomainRepository
     */
    private $domains;

    public function __construct(
        Action $action,
        DomainRepository $domains
    ) {
        $this->action = $action;

        $this->domains = $domains;
    }

    public function enable(string $name): bool
    {
        Logs::actions()->info("Enable worker {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            if (!$domain->deployed()) {
                throw new Exception("Domain must be deployed");
            }

            $this->action
                ->run(new DomainWorkerEnable($domain));
        } catch (Exception $e) {
            return $this->action
                ->report("Enable worker {$name}: {$e->getMessage()}");
        }

        return true;
    }

    public function disable(string $name): bool
    {
        Logs::actions()->info("Disable worker {$name}");

        try {
            $domain = $this->domains
                ->byName($name);

            if (!$domain->deployed()) {
                throw new Exception("Domain must be deployed");
            }

            $this->action
                ->run(new DomainWorkerDisable($domain));
        } catch (Exception $e) {
            return $this->action
                ->report("Disable worker {$name}: {$e->getMessage()}");
        }

        return true;
    }
}
