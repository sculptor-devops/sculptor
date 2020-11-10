<?php

namespace Sculptor\Agent\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Log\LoggerInterface;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * @method static actions(array $context = []): LoggerInterface
 * @method static security(array $context = []): LoggerInterface
 * @method static backup(array $context = []): LoggerInterface
 * @method static batch(array $context = []): LoggerInterface
 * @method static login(array $context = []): LoggerInterface
 * @method static job(array $context = []): LoggerInterface
 */
class Logs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sculptor\Agent\Logs\Logs::class;
    }
}
