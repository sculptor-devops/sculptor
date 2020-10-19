<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DomainStatusException extends Exception
{
    public function __construct(string $from, string $to, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Domain status cannot be changed from {$from} to {$to}", $code, $previous);
    }
}
