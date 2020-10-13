<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Illuminate\Support\Facades\File;

class Structure
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @throws Exception
     */
    public function create(): void {
        $root = '/home/' . SITES_USER . "/sites/{$this->name}";

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
