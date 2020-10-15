<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Support\Replacer;

class Crontab implements DomainAction
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        $filename = "{$domain->configs()}/cron.conf";

        $template = File::get($filename);

        $root = $domain->root();

        $compiled = Replacer::make($template)
            ->replace('{PATH}', $root)
            ->value();

        if (!File::put("{$root}/cron.conf", $compiled)) {
            throw new Exception("Cannot create crontab configuration from {$filename}");
        }

        return true;
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
