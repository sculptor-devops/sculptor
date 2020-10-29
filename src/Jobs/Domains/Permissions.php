<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\System;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

class Permissions implements DomainAction
{
    /**
     * @var System
     */
    private $system;

    /**
     * Certificates constructor.
     * @param System $system
     */
    public function __construct(System $system)
    {
        $this->system = $system;
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

        $this->system
            ->run($root, ['chmod', '-R', '755', "{$root}"]);

        $this->system
            ->run($root, ['chown', '-R', "{$user}:{$user}", "{$root}"]);

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
