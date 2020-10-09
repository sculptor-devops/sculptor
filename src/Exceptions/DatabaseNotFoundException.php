<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DatabaseNotFoundException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Cannot find database {$message}", $code, $previous);
    }
}
