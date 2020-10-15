<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Repositories\Entities\Domain;

class Structure implements DomainAction
{
    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function compile(Domain $domain): bool
    {
        $root = $domain->root();

        $templates = base_path("templates");

        foreach ([
                     $root,
                     "{$root}/certs",
                     "{$root}/configs",
                     "{$root}/logs",
                     "{$root}/shared"
                 ] as $folder) {

            if (File::exists($folder)) {
                continue;
            }

            if (!File::makeDirectory($folder, 0755, true)) {
                throw new Exception("Error creating {$folder}");
            }
        }

        foreach ([
            "{$domain->type}.deployer.php" => 'deployer.php',
            "{$domain->type}.cron" => 'cron.conf',
            "{$domain->type}.worker" => 'worker.conf',
            "{$domain->type}.env" => 'env',
            "{$domain->type}.nginx.conf" => 'nginx.conf',
            "{$domain->type}.logrotate.conf" => 'logrotate.conf',
                 ] as $filename => $destination) {

            if (!File::copy("{$templates}/{$filename}",
                "{$domain->configs()}/{$destination}")) {
                throw new Exception("Cannot create {$destination} template file type {$domain->type}");
            }
        }

        return true;
    }
}
