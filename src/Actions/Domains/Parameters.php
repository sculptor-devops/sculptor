<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Exceptions\DatabaseNotFoundException;
use Sculptor\Agent\Exceptions\DatabaseUserNotFoundException;
use Sculptor\Agent\Exceptions\ParameterInvalidException;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;
use Sculptor\Agent\Repositories\Entities\Domain;

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
        'user'
    ];
    /**
     * @var DatabaseRepository
     */
    private $databases;
    /**
     * @var DatabaseUserRepository
     */
    private $users;

    public function __construct(DatabaseRepository $databases, DatabaseUserRepository $users)
    {
        $this->databases = $databases;

        $this->users = $users;
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

        $domain->update(["{$name}" => "{$value}"]);

        return true;
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
