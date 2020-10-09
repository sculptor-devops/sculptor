<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DatabaseUserNotFoundException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Cannot find user {$message}", $code, $previous);
    }
}
