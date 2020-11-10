<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Jobs\Domains\Support\System;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Worker implements DomainAction
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
        Logs::actions()->debug("Worker setup for {$domain->name}");

        $template = $this->compiler
            ->load($domain->configs(), 'worker.conf', $domain->type);

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

        $this->system
            ->run(
                "{$domain->root()}",
                [
                    'supervisorctl',
                    'start',
                    "{$domain->name}:*"
                ]
            );

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

        $this->system
            ->run(
                $domain->root(),
                [
                    'supervisorctl',
                    'stop',
                    "{$domain->name}:*"
                ]
            );

        $this->system
            ->deleteIfExists("/etc/supervisor/conf.d/{$domain->name}.conf");

        $this->reload($domain);

        return true;
    }

    private function reload(Domain $domain): void
    {
        $this->system
            ->run(
                $domain->root(),
                [
                    'supervisorctl',
                    'reread'
                ]
            );

        $this->system
            ->run(
                $domain->root(),
                [
                    'supervisorctl',
                    'update'
                ]
            );
    }
}
