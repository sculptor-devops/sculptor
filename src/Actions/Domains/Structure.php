<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Repositories\Entities\Domain;

class Structure
{
    /**
     * @var Domain
     */
    private $domain;

    /**
     * Certificates constructor.
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @throws Exception
     */
    public function create(): void {
        $root = "/home/{$this->domain->user}/sites/{$this->domain->name}";

        foreach ([
                     $root,
                     "{$root}/certs",
                     "{$root}/config",
                     "{$root}/logs",
                 ] as $folder) {

            if (File::exists($folder)) {
                continue;
            }

            if (!File::makeDirectory($folder, 0755, true)) {
                throw new Exception("Error creating {$folder}");
            }
        }
    }
}
