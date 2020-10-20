<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Agent\Actions\Daemons as ServiceActions;

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
        Logs::actions()->debug("Webserver setup for {$domain->name}");

        $nginx = File::get("{$domain->configs()}/nginx.conf");

        $logrotate = File::get("{$domain->configs()}/logrotate.conf");

        $certificates = $this->compiler->certificates($domain);

        $nginx = $this->compiler
            ->replace($nginx, $domain)
            ->replace('{DOMAINS}', $domain->serverNames())
            ->replace('{CERTIFICATE}', $certificates['crt'])
            ->replace('{CERTIFICATE_KEY}', $certificates['key'])
            ->value();

        $logrotate = $this->compiler
            ->replace($logrotate, $domain)
            ->replace('{RETAIN}', "366")
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
        Logs::actions()->debug("Enabling www domain root {$domain->name}");

        if (!File::exists($domain->home())) {
            throw new Exception("Public directory {$domain->home()} not exists");
        }

        $enabled = $this->runner
            ->from('/etc/nginx/sites-enabled/')
            ->run([
                'ln',
                "-s",
                "/etc/nginx/sites-available/{$domain->name}.conf",
                '/etc/nginx/sites-enabled/'
            ]);

        if (!$enabled->success()) {
            throw new Exception("Error enabling {$domain->name}: {$enabled->error()}");
        }

        return $this->reload();
    }

    /**
     * @param string $from
     * @param string $filename
     * @throws Exception
     */
    private function remove(string $from, string $filename): void
    {
        if (!File::exists($filename)) {
            return;
        }

        $deleted = $this->runner
            ->from($from)
            ->run([ 'rm', $filename ]);

        if (!$deleted->success()) {
            throw new Exception("Error deleting configuration {$filename}: {$deleted->error()}");
        }
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function disable(Domain $domain): bool
    {
        Logs::actions()->debug("Disabling www domain {$domain->name}");

        $this->remove('/etc/nginx/sites-enabled/', "/etc/nginx/sites-enabled/{$domain->name}.conf");

        $this->remove('/etc/logrotate.d/', "/etc/logrotate.d/{$domain->name}.conf");

        return $this->reload();
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function reload(): bool
    {
        Logs::actions()->debug("Reloading services for www");

        foreach (config('sculptor.services')[DaemonGroupType::WEB] as $service) {
            if (!$this->daemons->reload($service)) {
                throw new Exception("Cannot reload service {$service}");
            }
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
        $this->remove('/etc/nginx/sites-available/', "/etc/nginx/sites-available/{$domain->name}.conf");

        return $this->disable($domain);
    }
}
