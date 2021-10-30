<?php

namespace Sculptor\Agent\Facades;

use Illuminate\Support\Facades\Facade;
use Sculptor\Agent\Configuration as Concrete;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * @method static get(string $string): string
 * @method static getArray(string $string): array 
 * @method static database(array $array)
 */
class Configuration extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Concrete::class;
    }
}
