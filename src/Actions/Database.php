<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Jobs\DatabaseCreate;
use Sculptor\Agent\Jobs\DatabaseDelete;
use Sculptor\Agent\Jobs\DatabaseUserCreate;
use Sculptor\Agent\Jobs\DatabaseUserDelete;
use Sculptor\Agent\Jobs\DatabaseUserPassword;
use Sculptor\Agent\Queues\Queues;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DatabaseUserRepository;

class Database extends Actions
{
    /**
     * @var DatabaseRepository
     */
    private $database;
    /**
     * @var DatabaseUserRepository
     */
    private $users;

    /**
     * Actions constructor.
     * @param Queues $queues
     * @param DatabaseRepository $database
     * @param DatabaseUserRepository $users
     */
    public function __construct(Queues $queues, DatabaseRepository $database, DatabaseUserRepository $users)
    {
        parent::__construct($queues);

        $this->database = $database;

        $this->users = $users;
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function create(string $name): bool
    {
        if ($this->database->exists($name)) {
            throw new Exception("Database {$name} already exists");
        }

        if ($this->run(new DatabaseCreate($name))) {
            $this->database->create(['name' => $name]);

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function delete(string $name): bool
    {
        $database = $this->database->byName($name);

        if ($this->run(new DatabaseDelete($name))) {
            foreach ($database->users as $user) {
                $this->drop($user->name, $name);
            }

            $database->delete();

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     * @throws ValidatorException
     * @throws Exception
     */
    public function user(string $name, string $password, string $db, string $host = 'localhost'): bool
    {
        $database = $this->database->byName($db);

        if ($this->run(new DatabaseUserCreate($name, $password, $db, $host))) {
            $this->users->create([
                'name' => $name,
                'database_id' => $database->id,
                'host' => $host,
                'password' => $password
            ]);

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     * @throws Exception
     */
    public function password(string $name, string $password, string $db, string $host = 'localhost'): bool
    {
        $database = $this->database->byName($db);

        $user = $this->users->byName($database, $name);

        if ($this->run(new DatabaseUserPassword($name, $db, $password, $host))) {
            $user->update([ 'password' => $password]);

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $db
     * @param string $host
     * @return bool
     * @throws Exception
     */
    public function drop(string $name, string $db, string $host = 'localhost'): bool
    {
        $database = $this->database->byName($db);

        $user = $this->users->byName($database, $name);

        if ($this->run(new DatabaseUserDelete($db, $name, $host))) {
            $user->delete();

            return true;
        }

        return false;
    }
}
