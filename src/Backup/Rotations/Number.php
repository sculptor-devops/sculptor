<?php

namespace Sculptor\Agent\Backup\Rotations;

use Sculptor\Agent\Backup\Contracts\Rotation;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Number implements Rotation
{
    public function name(): string
    {
        return 'number';
    }

    public function rotate(array $catalogs, int $number): array
    {
        $catalogs = collect($catalogs)
            ->sortByDesc('timestamp');

        if ($catalogs->count() < $number) {
            return [];
        }

        $pivot = $catalogs->take($number)->last();

        return $catalogs->where('timestamp', '>', $pivot['timestamp'])
            ->toArray();
    }
}
