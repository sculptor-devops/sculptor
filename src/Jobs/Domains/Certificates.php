<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Jobs\Domains\Support\System;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Certificates implements DomainAction
{
    /**
     * @var System
     */
    private $system;

    /**
     * Certificates constructor.
     * @param System $system
     */
    public function __construct(System $system)
    {
        $this->system = $system;
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
                $this->selfSigned($domain, "{$domain->root()}/certs");

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

        $this->system
            ->run(
                $path,
                [
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
                    "{$path}/{$domain->name}.key",
                    '-subj',
                    "/CN={$domain->name}"
                ]
            );

        // "/C=IT/ST=Italy/L=Italy/O=IT/CN=www.example.com"
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    private function letsEncrypt(Domain $domain): void
    {
        if ($domain->email == null) {
            throw new Exception("Domain {$domain->name} has no email configured");
        }

        if (!File::exists($domain->current())) {
            Logs::job()->notice("Public folder {$domain->name} not yet created, skipping let's encrypt registration");

            return;
        }

        if ($domain->status == DomainStatusType::DEPLOYED) {
            Logs::job()->warning("Creating let's encrypt need a {$domain->name} to be deployed");

            return;
        }

        Logs::job()->debug("Creating let's encrypt certificates for {{$domain->serverName()}} with email {$domain->email}");

        $command = collect([
            'certbot',
            'certonly',
            '--webroot',
            '--agree-tos',
            '-n',

            '-m',
            $domain->email,

            '--webroot-path',
            $domain->home(),

            '--deploy-hook',
            "/usr/bin/sculptor domain:certbot {$domain->name} deploy",
            '--pre-hook',
            "/usr/bin/sculptor domain:certbot {$domain->name} pre",

            '-d',
            $domain->name
        ]);

        foreach (explode(' ', $domain->alias) as $alias) {
            if (
                Str::of($alias)
                ->trim()
                ->isNotEmpty()
            ) {
                $command->push('-d')
                    ->push($alias);
            }
        }

        Logs::job()->debug("Calling certbot " . implode(' ', $command->toArray()));

        $this->system
            ->run("{$domain->root()}/certs", $command->toArray());
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    public function copy(Domain $domain): void
    {
        Logs::job()->debug("Copy certbot certificates of {$domain->name}");

        foreach (
            [
                     'cert.pem',
                     'chain.pem',
                     'fullchain.pem',
                     'privkey.pem'
                 ] as $cert
        ) {
            $from = "/etc/letsencrypt/live/{$domain->name}/{$cert}";

            $to = "{$domain->root()}/certs/{$domain->name}.{$cert}";

            if (!File::copy($from, $to)) {
                throw new Exception("Cannot copy certbot certificates from {$from} to {$to}");
            }

            Logs::job()->debug("Copy certbot certificates from {$from} to {$to}");
        }
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    public function apply(Domain $domain): void
    {
        Logs::job()->debug("Apply certbot certificates of {$domain->name}");

        $path = "{$domain->root()}/certs/{$domain->name}";

        foreach (["{$path}/.cert.pem" => "{$path}/.crt", "{$path}/.privkey.pem" => "{$path}.key"] as $from => $to) {
            if (!File::copy($from, $to)) {
                throw new Exception("Cannot copy certificate from {$from} to {$to}");
            }
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
