<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DaemonInvalidException extends Exception
{
    public function __construct(string $name = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Invalid service {$name}", $code, $previous);
    }
}
