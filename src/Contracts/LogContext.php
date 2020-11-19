<?php

namespace Sculptor\Agent\Contracts;

use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface LogContext
{
    public function emergency(string $message, array $context = array()): void;

    public function alert(string $message, array $context = array()): void;

    public function critical(string $message, array $context = array()): void;

    public function error(string $message, array $context = array()): void;

    public function warning(string $message, array $context = array()): void;

    public function notice(string $message, array $context = array()): void;

    public function info(string $message, array $context = array()): void;

    public function debug(string $message, array $context = array()): void;

    public function log(int $level, string $message, array $context = array()): void;

    /**
     * @param Throwable $e
     */
    public function report(Throwable $e): void;
}
