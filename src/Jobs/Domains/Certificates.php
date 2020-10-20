<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Logs\Logs;
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
    public function compile(Domain $domain): bool
    {
        Logs::actions()->debug("Certificate {$domain->name} type {$domain->certificate}");

        switch ($domain->certificate) {
            case CertificatesTypes::CUSTOM:
            case CertificatesTypes::SELF_SIGNED:
                $this->selfSigned($domain, "{$domain->root()}/certs");

                break;

            case CertificatesTypes::LETS_ENCRYPT:
                // $this->selfSigned($domain, "{$domain->root()}/certs");
                $this->letsEncrypt($domain);

                break;

            default:
                throw new Exception("Unknown certificate type {$domain->certificate}");
        }

        return true;
    }

    /**
     * @param Domain $domain
     * @param string $path
     * @throws Exception
     */
    private function selfSigned(Domain $domain, string $path): void
    {
        Logs::job()->debug("Creating self signed certificates in {$path}");

        $result = $this->runner
            ->from($path)
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
                "{$path}/{$domain->name}.crt",
                '-keyout',
                "{$path}{$domain->name}.key",
                '-subj',
                "/CN={$domain->name}"
            ]);

        // "/C=IT/ST=Italy/L=Italy/O=IT/CN=www.example.com"

        if (!$result->success()) {
            throw new Exception("Error creating self signed certificate: {$result->error()}");
        }
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    private function letsEncrypt(Domain $domain): void
    {
        $email = $domain->email;

        if ($email == null) {
            throw new Exception("Domain {$domain->name} has no email configured");
        }

        Logs::job()->debug("Creating let's encrypt certificates for {{$domain->serverName()}} with email {$email}");

        $names = str_replace($domain->serverName(), ' ', '-d ');

        $result = $this->runner
            ->from("{$domain->root()}/certs")
            ->run([
                'certbot',
                '--nginx ',
                '--agree-tos',
                '-n',
                '-m',
                $email,
                '-d',
                $names
            ]);

        if (!$result->success()) {
            throw new Exception("Error creating self signed certificate: {$result->error()}");
        }
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function delete(Domain $domain): bool
    {
        throw new Exception("Delete not implemented");
    }
}
