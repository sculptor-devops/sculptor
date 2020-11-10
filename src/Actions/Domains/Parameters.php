<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Exceptions\DatabaseNotFoundException;
use Sculptor\Agent\Exceptions\DatabaseUserNotFoundException;
use Sculptor\Agent\Exceptions\ParameterInvalidException;
use Sculptor\Agent\PasswordGenerator;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Parameters
{
    /**
     * @const array
     */
    public const ALLOWED = [
        'alias',
        'type',
        'certificate',
        'home',
        'deployer',
        'install',
        'vcs',
        'database',
        'user',
        'www',
        'email',
        'token',
        'provider',
        'branch'
    ];
    /**
     * @var DatabaseRepository
     */
    private $databases;
    /**
     * @var DatabaseUserRepository
     */
    private $users;
    /**
     * @var PasswordGenerator
     */
    private $password;

    public function __construct(
        DatabaseRepository $databases,
        DatabaseUserRepository $users,
        PasswordGenerator $password
    ) {
        $this->databases = $databases;

        $this->users = $users;

        $this->password = $password;
    }

    /**
     * @param Domain $domain
     * @param string $name
     * @param string $value
     * @return bool
     * @throws Exception
     */
    public function set(Domain $domain, string $name, string $value): bool
    {
        if (!in_array($name, Parameters::ALLOWED)) {
            throw new ParameterInvalidException($name);
        }

        if ($name == 'database') {
            $this->database($domain, $value);

            return true;
        }

        if ($name == 'user') {
            $this->user($domain, $value);

            return true;
        }

        if ($name == 'token') {
            $domain->update(['token', $this->password->token()]);

            return true;
        }

        $domain->update(["{$name}" => $this->normalize($value)]);

        return true;
    }

    private function normalize(string $value): string
    {
        if ($value == 'true') {
            return '1';
        }

        if ($value == 'false') {
            return '0';
        }

        return $value;
    }

    /**
     * @param Domain $domain
     * @param string $value
     * @throws DatabaseNotFoundException
     */
    private function database(Domain $domain, string $value): void
    {
        $database = $this->databases
            ->byName($value);

        $domain->database()
            ->associate($database)
            ->save();
    }

    /**
     * @param Domain $domain
     * @param string $value
     * @throws DatabaseUserNotFoundException
     */
    private function user(Domain $domain, string $value): void
    {
        $user = $this->users
            ->byName($domain->database, $value);

        $domain->databaseUser()
            ->associate($user)
            ->save();
    }
}
