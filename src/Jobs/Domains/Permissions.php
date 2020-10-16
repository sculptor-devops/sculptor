<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Logs\Logs;
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

        Logs::actions()->debug("Permissions setup for {$root} user {$user}");

        $permissions = $this->runner
            ->from($root)
            ->run(['chmod', '-R', '755', "{$root}"]);

        $ownership = $this->runner
            ->from($root)
            ->run(['chown', '-R', "{$user}:{$user}", "{$root}"]);

        if (!$permissions
            ->success()) {
            throw new Exception("Cannot change {$root} permissions to {$user}: {$permissions->error()}");
        }

        if (!$ownership->success()) {
            throw new Exception("Cannot change {$root} ownership to {$user}: {$ownership->error()}");
        }

        return true;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        return $this->compile($domain);
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function delete(Domain $domain): bool
    {
        throw new Exception("Delete not implemented");
    }
}
