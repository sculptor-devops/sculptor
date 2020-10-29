<?php

namespace Sculptor\Agent\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Log\LoggerInterface;

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
