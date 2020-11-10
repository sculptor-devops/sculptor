<?php

namespace Sculptor\Agent\Contracts;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface BlueprintRecord
{
    public function serialize(): array;

    public function serializeFiler(): array;
}
