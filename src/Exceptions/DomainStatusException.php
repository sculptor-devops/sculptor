<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class DomainStatusException extends Exception
{
    public function __construct(string $status, string $from, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Domain status cannot be changed to {$status} from {$from}", $code, $previous);
    }
}
