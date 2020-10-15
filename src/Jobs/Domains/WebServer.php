<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\Daemons;
use \Sculptor\Agent\Actions\Daemons as ServiceActions;

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
        $nginx = File::get("{$domain->configs()}/nginx.conf");

        $logrotate = File::get("{$domain->configs()}/logrotate.conf");

        $nginx = $this->compiler
            ->replace($nginx, $domain)
            ->replace('{DOMAINS}', $domain->serverNames())
            ->value();

        $logrotate = $this->compiler
            ->replace($logrotate, $domain)
            ->replace('{RETAIN}', 366)
            ->value();

        return $this->compiler
                ->save("/etc/nginx/sites-available/{$domain->name}.conf", $nginx) &&
            $this->compiler
                ->save("/etc/logrotate.d/{$domain->name}.conf", $logrotate);
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function enable(Domain $domain): bool
    {
        $enabled = $this->runner
            ->from('/etc/nginx/sites-enabled/')
            ->run([
                'ln',
                "/etc/nginx/sites-available/{$domain->name}.conf",
                '/etc/nginx/sites-enabled/'
            ]);

        if (!$enabled->success() ) {
            throw new Exception("Error enabling {$domain->name}: {$enabled->error()}");
        }

        return $this->reload();
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function disable(Domain $domain): bool
    {
        $disabled = $this->runner
            ->from('/etc/nginx/sites-enabled/')
            ->run([
                'rm',
                "/etc/nginx/sites-enabled/{$domain->name}.conf"
            ]);

        if (!$disabled->success() ) {
            throw new Exception("Error disabling {$domain->name}: {$disabled->error()}");
        }

        return $this->reload();
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function reload(): bool
    {
        foreach (ServiceActions::SERVICES['web'] as $service) {
            if (!$this->daemons->reload($service)) {
                throw new Exception("Cannot reload service {$service}");
            }
        }

        return true;
    }
}
