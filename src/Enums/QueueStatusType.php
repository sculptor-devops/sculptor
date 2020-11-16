<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class QueueStatusType extends Enum
{
    public const WAITING = 'waiting';

    public const RUNNING = 'running';

    public const ERROR = 'error';

    public const OK = 'ok';

    public const FINISHED_STATUSES =  [ QueueStatusType::ERROR, QueueStatusType::OK ];
}
