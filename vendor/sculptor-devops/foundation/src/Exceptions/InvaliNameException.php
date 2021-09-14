<?php namespace Sculptor\Foundation\Exceptions;

use Exception;
use Throwable;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class InvaliNameException extends Exception
{
    /**
     * FileNotFoundException constructor.
     * @param string $file
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $name, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("The name {$name} is invalid", $code, $previous);
    }
}
