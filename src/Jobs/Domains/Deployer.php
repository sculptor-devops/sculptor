<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
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
        $template = File::get("{$domain->configs()}/deployer.php");

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->replace('{REPOSITORY}', $domain->vcs)
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/deploy.php", $compiled);
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        $deploy = $this->runner
            ->from($domain->root())
            ->run([
                'dep',
                $domain->deployer ?? 'deploy'
            ]);

        if (!$deploy->success()) {
            throw new Exception("Error deploy: {$deploy->error()}");
        }

        return true;
    }
}
