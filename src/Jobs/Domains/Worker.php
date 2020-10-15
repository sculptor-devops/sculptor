<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Foundation\Support\Replacer;

class Worker implements DomainAction
{
    /**
     * @var Daemons
     */
    private $daemons;
    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Daemons $daemons, Runner $runner, Compiler $compiler)
    {
        $this->daemons = $daemons;

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
        $template = File::get("{$domain->configs()}/worker.conf");

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->replace('{COUNT}', 1)
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/worker.conf", $compiled);
    }
}
