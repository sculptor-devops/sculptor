<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Jobs\Domains\Support\System;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

class Deployer implements DomainAction
{
    /**
     * @var System
     */
    private $system;
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(System $system, Compiler $compiler)
    {
        $this->system = $system;

        $this->compiler = $compiler;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function compile(Domain $domain): bool
    {
        if ($domain->deployer == null) {
            Logs::actions()->debug("Deploy not enabled for {$domain->name}");

            return true;
        }

        Logs::actions()->debug("Deploy setup for {$domain->name}");

        $template = $this->compiler
            ->load($domain->configs(), 'deployer.php', $domain->type);

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->replace('{REPOSITORY}', $domain->vcs ?? 'git@not_defined.com/unknown/something.git')
            ->replace('{BRANCH}', $domain->branch ?? 'master')
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/deploy.php", $compiled);
    }

    /**
     * @param Domain $domain
     * @param string|null $command
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain, string $command = null): bool
    {
        $this->deploy('deploy:unlock', $domain);

        $deploy = $domain->deployer ?? SITES_DEPLOY;

        if ($domain->status == DomainStatusType::NEW) {
            $deploy = $domain->install ?? SITES_INSTALL;
        }

        if ($command != null) {
            $deploy = $command;
        }

        if ($deploy == null) {
            throw new Exception("Command deploy cannot be null for {$domain->name}");
        }

        $domain->update([ 'status' => DomainStatusType::DEPLOYING ]);

        $this->deploy($deploy, $domain);

        return true;
    }

    /**
     * @param string $command
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    private function deploy(string $command, Domain $domain): bool
    {
        Logs::actions()->debug("Deploy run {$command} on {$domain->name}");

        $this->system
            ->runAs(
                $domain->root(),
                $domain->user,
                [
                    'dep',
                    $command
                ],
                null
            );

        return true;
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
