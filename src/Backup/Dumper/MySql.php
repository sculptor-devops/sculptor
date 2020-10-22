<?php

namespace Sculptor\Agent\Backup\Dumper;

use Sculptor\Agent\Enums\BackupDatabaseType;
use Spatie\DbDumper\Databases\MySql as Driver;
use Sculptor\Agent\Backup\Contracts\Dumper;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class MySql implements Dumper
{

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return BackupDatabaseType::MYSQL;
    }

    /**
     * @param string $filename
     * @return bool
     * @throws CannotStartDump
     * @throws DumpFailed
     */
    public function dump(string $filename): bool
    {
        Driver::create()
            ->setHost($this->config['DB_HOST'])
            ->setPort($this->config['DB_PORT'])
            ->setDbName($this->config['DB_DATABASE'])
            ->setUserName($this->config['DB_USERNAME'])
            ->setPassword($this->config['DB_PASSWORD'])
            ->dumpToFile($filename);

        return true;
    }
}
