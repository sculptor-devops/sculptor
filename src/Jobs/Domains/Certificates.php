<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;

class Certificates implements DomainAction
{
    /**
     * @var Runner
     */
    private $runner;

    /**
     * Certificates constructor.
     * @param Runner $runner
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        switch ($domain->certificate) {
            case CertificatesTypes::CUSTOM:

            case CertificatesTypes::SELF_SIGNED:
                $this->selfSigned($domain);

                break;

            default:
                throw new Exception("Unknown certificate type {$domain->certificate}");
        }

        return true;
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    private function selfSigned(Domain $domain): void
    {
        $root = $domain->root();

        $result = $this->runner
            ->from("{$root}/certs")
            ->run([
                'openssl',
                'req',
                '-new',
                '-x509',
                '-days',
                '3650',
                '-nodes',
                '-sha256',
                '-out',
                "{$root}/certs/{$domain->name}.crt",
                '-keyout',
                "{$root}/certs/{$domain->name}.key",
                '-subj',
                "/CN={$domain->name}"
            ]);

        // "/C=IT/ST=Italy/L=Italy/O=IT/CN=www.example.com"

        if (!$result->success()) {
            throw new Exception("Error creating self signed certificate: {$result->error()}");
        }
    }
}
