<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Support\Replacer;

class Env implements DomainAction
{
    /**
     * @param Domain $domain
     * @return bool
     * @throws Exception
     */
    public function run(Domain $domain): bool
    {
        $filename = "{$domain->configs()}/env";

        $template = File::get($filename);

        $root = $domain->root();

        $database = $this->database($template, $domain);

        $compiled = $database
            ->replace('{NAME}', $domain->name)
            ->replace('{URL}', "https://{$domain->name}")
            ->value();

        if (!File::put("{$root}/shared/.env", $compiled)) {
            throw new Exception("Cannot create env configuration in {$root}");
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
}
