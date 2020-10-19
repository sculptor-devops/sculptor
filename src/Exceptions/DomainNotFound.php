<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DomainNotFound extends Exception
{
    public function __construct(string $name, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Domain {$name} was not found", $code, $previous);
    }
}
