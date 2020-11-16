<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class CertificatesTypes extends Enum
{
    public const SELF_SIGNED = 'self-signed';

    public const LETS_ENCRYPT = 'lets-encrypt';

    public const CUSTOM = 'custom';
}
