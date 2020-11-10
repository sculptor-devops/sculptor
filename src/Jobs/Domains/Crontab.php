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

class Crontab implements DomainAction
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
        $this->system
            ->run(SCULPTOR_HOME, ['crontab', '-u', $user, $filename]);

        return true;
    }
}
