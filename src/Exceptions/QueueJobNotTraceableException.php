<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class QueueJobNotTraceableException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Job does not implements traceable', $code, $previous);
    }
}
