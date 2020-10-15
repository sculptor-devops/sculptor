<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Support\Replacer;

class Deployer implements DomainAction
{
    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        $filename = "{$domain->configs()}/deployer.php";

        $template = File::get($filename);

        $root = $domain->root();

        $compiled = Replacer::make($template)
            ->replace('{NAME}', $domain->name)
            ->replace('{REPOSITORY}', $domain->vcs)
            ->replace('{USER}', $domain->user)
            ->replace('{PATH}', $root)
            ->value();

        if (!File::put("{$root}/deploy.php", $compiled)) {
            throw new Exception("Cannot create deploy configuration in {$root}");
        }

        return true;
    }
}
