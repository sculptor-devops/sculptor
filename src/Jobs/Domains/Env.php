<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Support\Replacer;

class Env implements DomainAction
{
    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Runner $runner, Compiler $compiler)
    {
        $this->runner = $runner;

        $this->compiler = $compiler;
    }

    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function compile(Domain $domain): bool
    {
        $filename = "{$domain->configs()}/env";

        $template = File::get($filename);

        $database = $this->database($template, $domain);

        $compiled = $this->compiler
            ->replace($database->value(), $domain)
            ->value();

        return $this->compiler
            ->save("{$domain->root()}/shared/.env", $compiled);
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
}
