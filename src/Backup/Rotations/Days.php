<?php

namespace Sculptor\Agent\Backup\Rotations;

use Sculptor\Agent\Backup\Contracts\Rotation;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Days implements Rotation
{
    public function name(): string
    {
        return 'days';
    }

    public function rotate(array $catalogs, int $number): array
    {
        $timestamp = now()->subDays($number);

        return collect($catalogs)
            ->sortByDesc('timestamp')
            ->where('timestamp', '>', $timestamp->timestamp)
            ->toArray();
    }
}
