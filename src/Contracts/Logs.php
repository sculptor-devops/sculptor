<?php

namespace Sculptor\Agent\Contracts;

use Sculptor\Agent\Logs\LogsContext;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface Logs
{
    /**
     * @param array $context
     * @return LogsContext
     */
    public function actions(array $context = []): LogsContext;

    /**
     * @param array $context
     * @return LogsContext
     */
    public function security(array $context = []): LogsContext;

    /**
     * @param array $context
     * @return LogsContext
     */
    public function backup(array $context = []): LogsContext;

    /**
     * @param array $context
     * @return LogsContext
     */
    public function batch(array $context = []): LogsContext;

    /**
     * @param array $context
     * @return LogsContext
     */
    public function login(array $context = []): LogsContext;

    /**
     * @param array $context
     * @return LogsContext
     */
    public function job(array $context = []): LogsContext;
}
