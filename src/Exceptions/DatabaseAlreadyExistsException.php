<?php


namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DatabaseAlreadyExistsException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Database {$message} already exists", $code, $previous);
    }
}
