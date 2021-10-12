<?php

namespace Sculptor\Agent\Exceptions;

use Exception;
use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class InvalidNumberOfParameters extends Exception
{
    public function __construct(int $expected)
    {
        parent::__construct("Invalid number of parameters {$expected} expected", 0);
    }
}
