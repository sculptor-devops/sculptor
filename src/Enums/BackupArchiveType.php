<?php

namespace Sculptor\Agent\Enums;

use Sculptor\Agent\Enums\Support\Enum;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupArchiveType extends Enum
{
    public const LOCAL = 'local';

    public const S3 = 's3';

    public const DROPBOX = 'dropbox';
}
