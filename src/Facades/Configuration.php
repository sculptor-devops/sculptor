<?php

namespace Sculptor\Agent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $string)
 * @method static database(array $array)
 */
class Configuration extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sculptor\Agent\Configuration::class;
    }
}
