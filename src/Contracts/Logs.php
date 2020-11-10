<?php

namespace Sculptor\Agent\Contracts;

use Psr\Log\LoggerInterface;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface Logs
{
    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function actions(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function security(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function backup(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function batch(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function login(array $context = []): LoggerInterface;

    /**
     * @param array $context
     * @return LoggerInterface
     */
    public function job(array $context = []): LoggerInterface;
}
