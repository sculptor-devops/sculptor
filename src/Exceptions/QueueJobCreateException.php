<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class QueueJobCreateException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Cannot create job', $code, $previous);
    }
}
