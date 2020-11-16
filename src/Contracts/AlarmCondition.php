<?php

namespace Sculptor\Agent\Contracts;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface AlarmCondition
{
    public function threshold(bool $alarmed, string $rearm, string $threshold): bool;

    public function act(): bool;

    public function context(): array;
}
