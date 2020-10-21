<?php

namespace Sculptor\Agent\Backup\Dumper;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Dumper\MySql;
use Sculptor\Agent\Backup\Contracts\Dumper;
use Sculptor\Agent\Enums\BackupDatabaseType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Factory
{
    public static function make(array $config, string $type = BackupDatabaseType::MYSQL): ?Dumper
    {
        switch ($type) {
            case BackupDatabaseType::MYSQL:
                return new Mysql($config);

            default:
                return null;
        }
    }
}
