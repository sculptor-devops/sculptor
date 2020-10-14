<?php

namespace Sculptor\Agent\Jobs\Domains;

use Sculptor\Agent\Repositories\Entities\Domain;

class Env
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
