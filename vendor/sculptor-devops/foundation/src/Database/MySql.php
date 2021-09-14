<?php namespace Sculptor\Foundation\Database;

use Exception;
use Illuminate\Support\Facades\DB;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Exceptions\InvaliNameException;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class MySql implements Database
{
    /**
     * @var string
     */
    private $error = 'Unknown error';

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function db(string $name): bool
    {
        try {
	    if (!$this->valid($name)) {
	        throw new InvaliNameException($name);
            }


            $this->statement("CREATE DATABASE IF NOT EXISTS {$name};", "Error creating database {$name}");

            return true;

        } catch(Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function drop(string $name): bool
    {
        try {
            if (!$this->valid($name)) {
                throw new InvaliNameException($name);
	    }

            $this->statement("DROP DATABASE IF EXISTS {$name};", "Error dropping database {$name}");

            return true;

        } catch(Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     */
    public function user(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        try {
            if (!$this->valid($user)) {
                throw new InvaliNameException($user);
	    }

            if (!$this->valid($db)) {
                throw new InvaliNameException($db);
            }

            if (!$this->dropUser($user, $host)) {
                return false;
            }

            $this->statement("CREATE USER {$user}@'{$host}' IDENTIFIED BY '{$password}'", "Error creating user {$user}@'{$host}");

            $this->statement("GRANT ALL PRIVILEGES ON {$db}.* TO '{$user}'@'{$host}';", "Error granting privileges to {$user}");

            $this->statement("FLUSH PRIVILEGES;", 'Error flushing privileges');

            return true;

        } catch (Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     */
    public function password(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        return $this->user($user, $password, $db, $host);
    }

    /**
     * @param string $user
     * @param string $host
     * @return bool
     */
    public function dropUser(string $user, string $host = 'localhost'): bool
    {
        try {
            if (!$this->valid($user)) {
                throw new InvaliNameException($user);
            }

            $this->statement("DROP USER IF EXISTS {$user}@'{$host}'", "Drop user error {$user}@'{$host}");

            return true;

        } catch (Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * @param string $query
     * @param string $error
     * @throws Exception
     */
    private function statement(string $query, string $error): void
    {
        $result = DB::connection('db_server')->statement($query);

        if (!$result) {
            throw new Exception($error);
        }
    }

    private function valid(string $name): bool
    {
        if (preg_match('/^[a-zA-Z]+[A-Za-z0-9_]*$/', $name)) {
            return true;
        }

        return false;
    }
}
