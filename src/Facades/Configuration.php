<?php

namespace Sculptor\Agent\Facades;

use Illuminate\Support\Facades\Facade;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
