<?php

namespace Sculptor\Agent\Actions\Domains;

use Sculptor\Agent\Repositories\Entities\Domain;

class Deployer
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

    public function create(): void
    {

    }
}
