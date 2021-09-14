<?php namespace Sculptor\Foundation\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
interface Database
{
    /**
     * @param string $name
     * @return bool
     */
    public function db(string $name): bool;

    /**
     * @param string $name
     * @return bool
     */
    public function drop(string $name): bool;

    /**
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     */
    public function user(string $user, string $password, string $db, string $host = 'localhost'): bool;

    /**
     * @param string $user
     * @param string $host
     * @return bool
     */
    public function dropUser(string $user, string $host = 'localhost'): bool;

    /**
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string $host
     * @return bool
     */
    public function password(string $user, string $password, string $db, string $host = 'localhost'): bool;

    /**
     * @return string
     */
    public function error(): string;
}
