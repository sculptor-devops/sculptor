<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;

class Permissions implements DomainAction
{
    /**
     * @var Runner
     */
    private $runner;

    /**
     * Certificates constructor.
     * @param Runner $runner
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function compile(Domain $domain): bool
    {
        $user = $domain->user;

        $root = $domain->root();

        if (!$this->runner
            ->from($root)
            ->run(['chmod', '-R', '755'])) {
            throw new Exception("Cannot change {$root} permissions to {$user}");
        }

        if (!$this->runner
            ->from($root)
            ->run(['chown', '-R', "{$user}:{$user}"])) {
            throw new Exception("Cannot change {$root} ownership to {$user}");
        }

        return true;
    }
}
