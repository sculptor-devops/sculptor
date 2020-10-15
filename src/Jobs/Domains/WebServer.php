<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Foundation\Support\Replacer;

class WebServer implements DomainAction
{
    /**
     * @var Daemons
     */
    private $daemons;
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Daemons $daemons, Runner $runner)
    {
        $this->daemons = $daemons;

        $this->runner = $runner;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        $filename = "{$domain->configs()}/nginx.conf";

        $template = File::get($filename);

        $root = $domain->root();

        $compiled = Replacer::make($template)
            ->replace('{DOMAINS}', $domain->serverNames())
            ->replace('{NAME}', $domain->name)
            ->replace('{PATH}',  $root)
            ->replace('{USER}',  $domain->user)
            ->value();

        if (!File::put("/etc/nginx/sites-available/{$domain->name}.conf", $compiled)) {
            throw new Exception("Cannot create nginx configuration from {$filename}");
        }

        return true;
    }
}
