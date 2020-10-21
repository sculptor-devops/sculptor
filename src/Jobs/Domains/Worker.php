<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\Daemons;

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
        Logs::actions()->debug("Worker setup for {$domain->name}");

        $template = File::get("{$domain->configs()}/worker.conf");

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->replace('{COUNT}', "1")
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/worker.conf", $compiled);
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function delete(Domain $domain): bool
    {
        Logs::actions()->debug("Deleting worker for {$domain->name}");

        return $this->disable($domain);
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function enable(Domain $domain): bool
    {
        $worker = File::get("{$domain->root()}/worker.conf");

        if (!File::put("/etc/supervisor/conf.d/{$domain->name}.conf", $worker)) {
            throw new Exception("Cannot write worker configuration of {$domain->name}");
        }

        $this->reload($domain);

        $this->runner
            ->runOrFail([
                'supervisorctl',
                'start',
                "{$domain->name}:*"
            ]);

        return true;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function disable(Domain $domain): bool
    {
        if (!File::exists("/etc/supervisor/conf.d/{$domain->name}.conf")) {
            return true;
        }

        $this->runner
            ->runOrFail([
                'supervisorctl',
                'stop',
                "{$domain->name}:*"
            ]);


        if (!File::delete("/etc/supervisor/conf.d/{$domain->name}.conf")) {
            throw new Exception("Cannot delete worker configuration of {$domain->name}");
        }

        $this->reload($domain);

        return true;
    }

    private function reload(Domain $domain): void
    {
        $runner = $this->runner
            ->from($domain->root());

        $runner->runOrFail([
            'supervisorctl',
            'reread'
        ]);

        $runner->runOrFail([
            'supervisorctl',
            'update'
        ]);
    }
}
