<?php

namespace Sculptor\Agent\Jobs\Domains;

use Exception;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Contracts\DomainAction;
use Sculptor\Agent\Jobs\Domains\Support\Compiler;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Support\Replacer;
use Sculptor\Foundation\Services\EnvParser;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Env implements DomainAction
{
    /**
     * @var Compiler
     */
    private $compiler;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Compiler $compiler, Configuration $configuration)
    {
        $this->compiler = $compiler;

        $this->configuration = $configuration;
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

            if (
                !$this->compiler
                ->save("{$domain->root()}/{$destination}", $compiled)
            ) {
                throw new Exception("Unable to save env {$destination}/{$filename}");
            }
        }

        return true;
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

    /**
     * @param string $template
     * @param Domain $domain
     * @return Replacer
     */
    private function database(string $template, Domain $domain): Replacer
    {
        $database = 'default';

        $username = 'username';

        $password = 'secret';

        $host = '127.0.0.1';

        $driver = 'mysql';

        $port = '3306';

        $db = $domain->database;

        $user = $domain->databaseUser;

        if ($db) {
            $database = $db->name;

            $connection = $this->configuration->connection($db->driver);

            $host = $connection['host'];

            $port = $connection['port'];

            $driver = $db->driver;
        }

        if ($user) {
            $username = $user->name;

            $password = $user->password;
        }

        return Replacer::make($template)
            ->replace('{KEY}', $this->currentKey($domain))
            ->replace('{DATABASE_DRIVER}', $driver)
            ->replace('{DATABASE_HOST}', $host)
            ->replace('{DATABASE_PORT}', $port)
            ->replace('{DATABASE}', $database)
            ->replace('{DATABASE_USERNAME}', $username)
            ->replace('{DATABASE_PASSWORD}', $password);
    }

    /**
     * @param Domain $domain
     * @return string
     */
    private function newKey(Domain $domain)
    {
        $config = "{$domain->current()}/config/app.php";

        $cipher = config('app.cipher');

        if (File::exists($config)) {
            $app = include($config);

            $cipher = $app['cipher'];
        }

        return 'base64:' . base64_encode(Encrypter::generateKey($cipher));
    }

    /**
     * @param Domain $domain
     * @return string
     */
    private function currentKey(Domain $domain): string
    {
        $newKey = $this->newKey($domain);

        $filename = "{$domain->current()}/.env";

        if (!File::exists($filename)) {
            return $newKey;
        }

        $parser = new EnvParser($filename);

        $key = $parser->get("APP_KEY", false);

        if ($key == null) {
            return $newKey;
        }

        return $key;
    }
}
