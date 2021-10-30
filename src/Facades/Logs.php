<?php

namespace Sculptor\Agent\Facades;

use Illuminate\Support\Facades\Facade;
use Sculptor\Agent\Logs\LogsContext;
use Sculptor\Agent\Logs\Logs as LogsClass;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * @method static LogsContext actions(array $context = [])
 * @method static LogsContext security(array $context = [])
 * @method static LogsContext backup(array $context = [])
 * @method static LogsContext batch(array $context = [])
 * @method static LogsContext login(array $context = [])
 * @method static LogsContext job(array $context = [])
 */
class Logs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LogsClass::class;
    }
}
