<?php

namespace Sculptor\Agent\Actions;

use Sculptor\Agent\Queues\Queues;

class Domains extends Base
{
    public function __construct(Queues $queues)
    {
        parent::__construct($queues);
    }

    public function create(
        string $name,
        string $aliases,
        string $type = 'laravel',
        string $certificate = 'self-signed',
        string $user = 'www'
    ): bool {

    }

    public function delete(string $name): bool
    {

    }

    public function configure(string $name): bool
    {

    }

    public function deploy(string $name): bool
    {

    }
}
