<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class ParameterInvalidException extends Exception
{
    public function __construct($name = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Invalid parameter {$name}", $code, $previous);
    }
}
