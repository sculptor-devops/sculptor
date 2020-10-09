<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class QueueJobRefUndefinedException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Queue ref is null', $code, $previous);
    }
}
