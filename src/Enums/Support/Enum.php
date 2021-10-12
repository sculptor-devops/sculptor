<?php

namespace Sculptor\Agent\Enums\Support;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use ReflectionClass;

class Enum
{
    public static function toArray(): array
    {
        $reflected = new ReflectionClass(static::class);

        $constants = collect($reflected->getConstants());

        if ($constants->count() == 0) {
            return [];
        }

        return $constants
            ->flatten()
            ->toArray();
    }

    public static function has(string $name): bool
    {
        return in_array($name, static::toArray());
    }
}
