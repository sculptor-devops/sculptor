<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Jobs\Domains\Support\System;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

class WebServer implements DomainAction
{
    /**
     * @var System
     */
    private $system;
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(System $system, Compiler $compiler)
    {
        $this->system = $system;

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

        $certificates = $this->compiler
            ->certificates($domain);

        foreach (
            [
                'nginx.conf' => '/etc/nginx/sites-available',
                'logrotate.conf' => '/etc/logrotate.d'
            ] as $filename => $destination) {
            $content = $this->compiler
                ->load($domain->configs(), 'nginx.conf', $domain->type);

            $compiled = $this->compiler
                ->replace($content, $domain)
                ->replace('{DOMAINS}', $domain->serverNames())
                ->replace('{CERTIFICATE}', $certificates['crt'])
                ->replace('{CERTIFICATE_KEY}', $certificates['key'])
                ->replace('{RETAIN}', "366")
                ->value();

            if (!$this->compiler
                ->save("{$destination}/{$filename}", $compiled)) {
                throw new Exception("Unable to write {$destination}/{$filename}");
            }
        }

        return true;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function enable(Domain $domain): bool
    {
        $origin = "/etc/nginx/sites-available/{$domain->name}.conf";

        $destination = "/etc/nginx/sites-enabled/{$domain->name}.conf";

        if (File::exists($destination) &&
            File::exists($origin)) {
            return true;
        }

        Logs::actions()
            ->debug("Enabling www domain root {$domain->name}");

        $this->system
            ->deleteIfExists($destination);

        $this->system
            ->errorIfNotExists($origin);

        $this->system
            ->errorIfNotExists($domain->home());

        $this->system
            ->run(
                '/etc/nginx/sites-enabled/',
                [
                    'ln',
                    "-s",
                    $origin,
                    $destination
                ]
            );

        return $this->reload();
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function disable(Domain $domain): bool
    {
        Logs::actions()
            ->debug("Disabling www domain {$domain->name}");

        $this->system
            ->deleteIfExists("/etc/nginx/sites-enabled/{$domain->name}.conf");

        $this->system
            ->deleteIfExists("/etc/logrotate.d/{$domain->name}.conf");

        return $this->reload();
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function reload(): bool
    {
        Logs::actions()
            ->debug("Reloading services for www");

        dispatch(new DaemonService(DaemonGroupType::WEB, DaemonOperationsType::RELOAD));

        return true;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function delete(Domain $domain): bool
    {
        Logs::actions()
            ->debug("Deleting www domain {$domain->name}");

        $this->system
            ->deleteIfExists("/etc/nginx/sites-available/{$domain->name}.conf");

        $this->system
            ->deleteIfExists("/etc/nginx/sites-enabled/{$domain->name}.conf");

        $this->system
            ->deleteIfExists("/etc/logrotate.d/{$domain->name}.conf");

        return $this->reload();
    }
}
