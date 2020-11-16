<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DaemonGroupType extends Enum
{
    public const WEB = 'web';

    public const QUEUE = 'queue';

    public const DATABASE = 'database';

    public const REMOTE = 'remote';
}
