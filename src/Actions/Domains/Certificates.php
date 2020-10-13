<?php

namespace Sculptor\Agent\Actions\Domains;

class Certificates
{
    public function __construct(string $name, string $aliases, string $type)
    {
        $this->name = $name;

        $this->aliases = $aliases;

        $this->type = $type;
    }

    public function create(): void
    {

    }
}
