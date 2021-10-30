<?php

namespace Sculptor\Agent\Backup\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Rotation
{
    public function name(): string;

    public function rotate(array $catalogs, int $number, string $destination, bool $dry = false): array;
}
