<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\System;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Structure implements DomainAction
{
    /**
     * @var System
     */
    private $system;

    public function __construct(System $system)
    {
        $this->system = $system;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function compile(Domain $domain): bool
    {
        $root = $domain->root();

        $templates = base_path("templates");

        Logs::actions()->debug("Structuring {$domain->name} root {$root}");

        foreach (
            [
                $root,
                // "{$domain->home()}",
                "{$root}/certs",
                "{$root}/configs",
                "{$root}/logs",
                "{$root}/shared"
            ] as $folder
        ) {
            if (File::exists($folder)) {
                continue;
            }

            if (!File::makeDirectory($folder, 0755, true)) {
                throw new Exception("Error creating {$folder}");
            }
        }

        foreach (
            [
                "{$domain->type}/deployer.php" => 'deployer.php',
                "{$domain->type}/cron" => 'cron.conf',
                "{$domain->type}/worker" => 'worker.conf',
                "{$domain->type}/env" => 'env',
                "{$domain->type}/nginx.conf" => 'nginx.conf',
                "{$domain->type}/logrotate.conf" => 'logrotate.conf',
                "{$domain->type}//ssh_config" => 'ssh_config'
            ] as $filename => $destination
        ) {
            $source = "{$templates}/{$filename}";

            $to = "{$domain->configs()}/{$destination}";

            Logs::actions()->debug("Structuring copy {$source} to {$to}");

            if (!File::copy($source, $to)) {
                throw new Exception("Cannot create {$destination} template file type {$domain->type}");
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
        Logs::actions()->debug("Deleting domain root {$domain->root()}");

        $this->system
            ->run(
                SITES_HOME . "/{$domain->user}",
                [
                    'rm',
                    '-rf',
                    $domain->root()
                ]
            );

        return true;
    }
}
