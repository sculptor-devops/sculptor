<?php

namespace Sculptor\Agent\Enums;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class LogContextType
{
    public const ACTIONS = 'actions';

    public const SECURITY = 'security';

    public const BACKUP = 'backup';

    public const LOGIN = 'login';

    public const BATCH = 'batch';

    public const JOB = 'job';
}
