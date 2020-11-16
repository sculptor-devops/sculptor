<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class LogContextType extends Enum
{
    public const ACTIONS = 'actions';

    public const SECURITY = 'security';

    public const BACKUP = 'backup';

    public const LOGIN = 'login';

    public const BATCH = 'batch';

    public const JOB = 'job';
}
