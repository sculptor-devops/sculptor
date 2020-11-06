<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Support\Replacer;

class Env implements DomainAction
{
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function compile(Domain $domain): bool
    {
        Logs::actions()->debug("Env setup for {$domain->name}");

        foreach (['env' => 'shared/.env', 'ssh_config' => 'ssh_config'] as $filename => $destination) {
            $content = $this->compiler->load($domain->configs(), $filename, $domain->type);

            $database = $this->database($content, $domain);

            $compiled = $this->compiler
                ->replace($database->value(), $domain)
                ->value();

            if (!$this->compiler
                ->save("{$domain->root()}/{$destination}", $compiled)) {
                throw new Exception("Unable to save env {$destination}/{$filename}");
            }
        }

        return true;
    }

    private function database(string $template, Domain $domain): Replacer
    {
        $database = 'default';

        $username = 'username';

        $password = 'secret';

        $db = $domain->database;

        $user = $domain->databaseUser;

        if ($db) {
            $database = $db->name;
        }

        if ($user) {
            $username = $user->name;

            $password = $user->password;
        }

        return Replacer::make($template)
            ->replace('{DATABASE}', $database)
            ->replace('{DATABASE_USERNAME}', $username)
            ->replace('{DATABASE_PASSWORD}', $password);
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
