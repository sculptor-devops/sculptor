<?php

namespace Tests\Stubs;

use Sculptor\Foundation\Contracts\Database;

class MySql implements Database
{
    private $result;

    public function __construct(bool $result = true)
    {
        $this->result = $result;
    }

    public function db(string $name): bool
    {
        return $this->result;
    }

    public function drop(string $name): bool
    {
        return $this->result;
    }

    public function user(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        return $this->result;
    }

    public function dropUser(string $user, string $host = 'localhost'): bool
    {
        return $this->result;
    }

    public function password(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        return $this->result;
    }

    public function error(): string
    {
        return 'no error';
    }
}
