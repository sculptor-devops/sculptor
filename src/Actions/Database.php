<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Exceptions\DatabaseAlreadyExistsException;
use Sculptor\Agent\Exceptions\DatabaseNotFoundException;
use Sculptor\Agent\Jobs\DatabaseCreate;
use Sculptor\Agent\Jobs\DatabaseDelete;
use Sculptor\Agent\Jobs\DatabaseUserCreate;
use Sculptor\Agent\Jobs\DatabaseUserDelete;
use Sculptor\Agent\Jobs\DatabaseUserPassword;
use Sculptor\Agent\Logs\Logs;
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
     */
    public function create(string $name): bool
    {
        Logs::actions()->info("Create database {$name}");

        try {
            if ($this->database->exists($name)) {
                throw new DatabaseAlreadyExistsException($name);
            }

            $this->run(new DatabaseCreate($name));

            $this->database->create(['name' => $name]);

            return true;
        } catch (Exception $e) {
            $this->report("Create database: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function delete(string $name): bool
    {
        Logs::actions()->info("Delete database {$name}");

        try {
            $database = $this->database->byName($name);

            $this->run(new DatabaseDelete($name));

            foreach ($database->users as $user) {
                $this->drop($user->name, $name);
            }

            $database->delete();

            return true;
        } catch (Exception $e) {
            $this->report("Delete database: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * @param string $name
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     * @throws Exception
     */
    public function user(string $name, string $password, string $db, string $host = 'localhost'): bool
    {
        Logs::actions()->info("Create user {$name}@{$host} on {$name}");

        try {
            $database = $this->database->byName($db);

            $this->run(new DatabaseUserCreate($name, $password, $db, $host));

            $this->users->create([
                'name' => $name,
                'database_id' => $database->id,
                'host' => $host,
                'password' => $password
            ]);

            return true;
        } catch (Exception $e) {
            $this->report("Create user: {$e->getMessage()}");

            return false;
        }
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
        Logs::actions()->info("Change password to {$name}@{$host} on {$name}");

        try {
            $database = $this->database->byName($db);

            $user = $this->users->byName($database, $name);

            $this->run(new DatabaseUserPassword($name, $db, $password, $host));

            $user->update([ 'password' => $password]);

            return true;
        } catch (Exception $e) {
            $this->report("Change password to: {$e->getMessage()}");

            return false;
        }
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
        Logs::actions()->info("Drop user {$name}@{$host} on {$name}");

        try {
            $database = $this->database->byName($db);

            $user = $this->users->byName($database, $name);

            $this->run(new DatabaseUserDelete($db, $name, $host));

            $user->delete();

            return true;
        } catch (Exception $e) {
            $this->report("Drop user: {$e->getMessage()}");

            return false;
        }
    }

    private function report(string $message): void
    {
        Logs::actions()->error($message);

        $this->error = "Error {$message}";
    }
}
