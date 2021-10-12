<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupRotationType extends Enum
{
    public const NUMBER = 'number';

    public const DAYS = 'days';
}
