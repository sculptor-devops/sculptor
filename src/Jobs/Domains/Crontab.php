<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;

class Crontab implements DomainAction
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
        Logs::actions()->debug("Crontab for {$domain->name}");

        $template = File::get("{$domain->configs()}/cron.conf");

        $compiled = $this->compiler
            ->replace($template, $domain)
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/cron.conf", $compiled);
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function delete(Domain $domain): bool
    {
        Logs::actions()->debug("Deleting crontab for {$domain->name}");

        return true;
    }

    public function update(string $filename, string $user): bool
    {
        $this->runner
            ->from(SCULPTOR_HOME)
            ->runOrFail(['crontab', '-u', $user, $filename]);

        return true;
    }
}
