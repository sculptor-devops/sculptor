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

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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

        $deployer = "{$domain->root()}/deploy.php";

        $template = $this->compiler
            ->load($domain->configs(), 'deployer.php', $domain->type);

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->replace('{REPOSITORY}', $domain->vcs ?? 'git@not_defined.com/unknown/something.git')
            ->replace('{BRANCH}', $domain->branch ?? 'master')
            ->value();

        $this->system
            ->saveAs($deployer, $compiled, $domain->user);

        $this->prepare($domain);

        return true;
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    private function prepare(Domain $domain): void
    {
        if (File::exists($domain->current())) {
            return;
        }

        $this->deploy('deploy:prepare', $domain);

        $this->deploy('deploy:release', $domain);

        $this->deploy('deploy:symlink', $domain);
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

        $domain->update(['status' => DomainStatusType::DEPLOYING]);

        $this->deploy($deploy, $domain);

        return true;
    }

    /**
     * @param string $command
     * @param Domain $domain
     * @param bool $worker
     * @return void
     * @throws Exception
     */
    private function deploy(string $command, Domain $domain): void
    {
        if ($domain->vcs == null) {
            Logs::actions()->info("Deploy {$domain->name} skipped, no vcs defined");
            return;
        }

        Logs::actions()->info("Deploy run {$command} on {$domain->name}");

        $user = $domain->user;

        if ($worker) {
            $user = whoami();
        }

        $this->system
            ->runAs(
                $domain->root(),
                $user,
                [
                    'dep',
                    $command,
                    '--log',
                    $domain->logs('deploy.log')
                ],
                null,
                function ($type, $buffer) {
                    Logs::job()->notice("Deployer ({$type}): {$buffer}");
                }
            );
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
