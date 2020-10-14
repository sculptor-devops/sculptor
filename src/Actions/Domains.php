<?php

namespace Sculptor\Agent\Actions;

use Sculptor\Agent\Queues\Queues;
use Sculptor\Agent\Repositories\DomainRepository;

class Domains extends Base
{
    /**
     * @var DomainRepository
     */
    private $domains;

    public function __construct(Queues $queues, DomainRepository $domains)
    {
        parent::__construct($queues);

        $this->domains = $domains;
    }

    public function create(
        string $name,
        string $aliases,
        string $type = 'laravel',
        string $certificate = 'self-signed',
        string $user = 'www'
    ): bool {

        $domain = $this->domains->create([
            'name' => $name,
            'aliases' => $aliases,
            'type' => $type,
            'certificate' => $certificate,
            'user' => $user
        ]);


        return true;
    }

    public function delete(string $name): bool
    {
        return true;
    }

    public function configure(string $name): bool
    {
        return true;
    }

    public function deploy(string $name): bool
    {
        return true;
    }
}
