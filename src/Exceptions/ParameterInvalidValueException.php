<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ParameterInvalidValueException extends Exception
{
    public function __construct($name = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Invalid parameter value {$name}", $code, $previous);
    }
}
