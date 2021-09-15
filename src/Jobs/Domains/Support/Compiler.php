<?php

namespace Sculptor\Agent\Jobs\Domains\Support;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Support\Replacer;
use Sculptor\Agent\Support\PhpVersions;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Compiler
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var PhpVersions
     */
    private $php;

    public function __construct(Configuration $configuration, PhpVersions $php)
    {
        $this->configuration = $configuration;

        $this->php = $php;
    }

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
            ->replace('{NAME}', $domain->name)
            ->replace('{PATH}', $domain->root())
            ->replace('{PUBLIC}', $domain->home())
            ->replace('{CURRENT}', $domain->current())
            ->replace('{HOME}', $domain->home)
            ->replace('{USER}', $domain->user)
            ->replace('{PHP}', $this->php->path($domain->engine ?? ENGINE_VERSION))
            ->replace('{PHP_VERSION}', $domain->engine);
    }

    /**
     * @param string $path
     * @param string $filename
     * @param string $type
     * @return string
     */
    public function load(string $path, string $filename, string $type): string
    {
        if (!File::exists("{$path}/{$filename}")) {
            $templates = base_path("templates/{$type}");

            File::copy("{$templates}/{$filename}", "{$path}/{$filename}");
        }

        return File::get("{$path}/{$filename}");
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

        switch ($domain->certificate) {
            case CertificatesTypes::SELF_SIGNED:
            case CertificatesTypes::CUSTOM:
                return [ 'crt' => "{$certs}.crt", 'key' => "{$certs}.key"];

            case CertificatesTypes::LETS_ENCRYPT:
                return [ 'crt' => "{$certs}.fullchain.pem", 'key' => "{$certs}.privkey.pem"];
        }

        throw new Exception("Invalid certificate type {$domain->type}");
    }
}
