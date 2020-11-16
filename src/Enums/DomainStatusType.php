<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainStatusType extends Enum
{
    public const NEW = 'new';

    public const CONFIGURED = 'configured';

    public const SETUP = 'setup';

    public const DEPLOYING = 'deploying';

    public const ERROR = 'error';

    public const DEPLOYED = 'deployed';
}
