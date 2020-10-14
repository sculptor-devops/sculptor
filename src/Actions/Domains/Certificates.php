<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;

class Certificates
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
    public function create(): void
    {
        switch ($this->domain->type) {
            case CertificatesTypes::SELF_SIGNED:
                $this->selfSigned();

                break;

            default:
                throw new Exception("Unknown certificate type {$this->domain->type}");
        }
    }

    /**
     *
     */
    private function selfSigned(): void
    {

    }
}
