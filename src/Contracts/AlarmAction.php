<?php

namespace Sculptor\Agent\Contracts;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface AlarmAction
{
    public function emit(string $to, string $message, array $context): void;
}
