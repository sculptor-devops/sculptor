<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\DomainType;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;

class Deployer implements DomainAction
{
    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Runner $runner, Compiler $compiler)
    {
        $this->runner = $runner;

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

        $template = File::get("{$domain->configs()}/deployer.php");

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->replace('{REPOSITORY}', $domain->vcs ?? $this->repo($domain))
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/deploy.php", $compiled);
    }

    private function repo(Domain $domain): string
    {
        switch ($domain->type) {
            case DomainType::LARAVEL:
                return 'https://github.com/laravel/laravel.git';

            default:
                return 'git@github.com/username/repository.git';
        }
    }

    /**
     * @param Domain $domain
     * @param string|null $command
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain, string $command = null): bool
    {
        $this->deploy('deploy:unlock', $domain, false);

        $deploy = $domain->deployer ?? SITES_DEPLOY;

        if (!File::exists($domain->home())) {
            $deploy = $domain->install ?? SITES_INSTALL;
        }

        if ($command != null) {
            $deploy = $command;
        }

        $this->deploy($deploy, $domain);

        return true;
    }

    /**
     * @param string $command
     * @param Domain $domain
     * @param bool $check
     * @return bool
     * @throws Exception
     */
    private function deploy(string $command, Domain $domain, bool $check = true): bool
    {
        Logs::actions()->debug("Deploy run {$command} on {$domain->name}");

        $deploy = $this->runner
            ->from($domain->root())
            ->run([
                'sudo',
                '-u',
                "{$domain->user}",
                'dep',
                $command
            ]);

        if (!$deploy->success() && $check) {
            throw new Exception("Error deploy: {$deploy->error()}");
        }

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
