<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DatabaseInvalidNameException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Database name {$message} is invalid", $code, $previous);
    }
}
