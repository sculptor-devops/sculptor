<?php

namespace Sculptor\Agent\Jobs\Domains\Support;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Support\Replacer;

class Compiler
{
    /**
     * @param string $template
     * @param Domain $domain
     * @return Replacer
     */
    public function replace(string $template, Domain $domain): Replacer
    {
        return Replacer::make($template)
            ->replace('{DOMAINS}', $domain->serverNames())
            ->replace('{URL}', "https://{$domain->name}")
            ->replace('{USER}', $domain->user)
            ->replace('{NAME}', $domain->name)
            ->replace('{PATH}', $domain->root())
            ->replace('{HOME}', $domain->home)
            ->replace('{PUBLIC}', $domain->home())
            ->replace('{USER}', $domain->user);
    }

    /**
     * @param string $filename
     * @param string $compiled
     * @return bool
     * @throws Exception
     */
    public function save(string $filename, string $compiled): bool
    {
        if (!File::put($filename, $compiled)) {
            throw new Exception("Cannot create configuration {$filename}");
        }

        return true;
    }

    /**
     * @param Domain $domain
     * @return array|string[]
     * @throws Exception
     */
    public function certificates(Domain $domain): array
    {
        $certs = "{$domain->root()}/certs/{$domain->name}";

        switch ($domain->type) {
            case CertificatesTypes::SELF_SIGNED:
            case CertificatesTypes::CUSTOM:
                return [ 'crt' => "{$certs}.crt", 'key' => "{$certs}.key"];

            case CertificatesTypes::LETS_ENCRYPT:
                return [];
        }

        throw new Exception("Invalid certificate type {$domain->type}");
    }
}
