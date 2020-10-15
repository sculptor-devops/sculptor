<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
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
        $template = File::get("{$domain->configs()}/cron.conf");

        $compiled = $this->compiler->replace($template, $domain)
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/cron.conf", $compiled);
    }

    /* private function add(string $filename, string $destination, string $user): bool
    {
        if (!$this->write($destination, $this->template($filename), "Cannot write to {$destination}")) {
            return false;
        }

        $this->command(['crontab', '-u', $user, $destination]);

        return true;
    }*/
}
