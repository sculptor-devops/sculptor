<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupStatusType extends Enum
{
    public const NONE = 'none';

    public const OK = 'ok';

    public const RUNNING = 'running';

    public const ERROR = 'error';
}
