<?php


namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

class QueueJobTimeoutException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Job Timeout', $code, $previous);
    }
}
