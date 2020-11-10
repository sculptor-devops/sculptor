<?php

namespace Sculptor\Agent\Enums;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DaemonOperationsType
{
    public const START = 'start';

    public const STOP = 'stop';

    public const RELOAD = 'reload';

    public const RESTART = 'restart';

    public const ENABLE = 'enable';

    public const DISABLE = 'disable';
}
