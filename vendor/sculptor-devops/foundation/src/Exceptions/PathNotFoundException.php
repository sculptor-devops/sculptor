<?php namespace Sculptor\Foundation\Exceptions;

use Exception;
use Throwable;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class PathNotFoundException extends Exception
{
    /**
     * PathNotFoundException constructor.
     * @param string $path
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $path, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("PATH {$path} NOT FOUND", $code, $previous);
    }
}
