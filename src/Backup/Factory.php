<?php

namespace Sculptor\Agent\Backup;

use Exception;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Factory
{
    /**
     * @var Database
     */
    private $database;
    /**
     * @var Domain
     */
    private $domain;
    /**
     * @var Blueprint
     */
    private $blueprint;

    public function __construct(Database $database, Domain $domain, Blueprint $blueprint)
    {
        $this->database = $database;

        $this->domain = $domain;

        $this->blueprint = $blueprint;
    }

    /**
     * @param Backup $backup
     * @return BackupInterface
     * @throws Exception
     */
    public function make(Backup $backup): BackupInterface
    {
        switch ($backup->type) {
            case BackupType::DATABASE:
                return $this->database;

            case BackupType::DOMAIN:
                return $this->domain;

            case BackupType::BLUEPRINT:
                return $this->blueprint;
        }

        throw new Exception("Invalid backup type {$backup->type}");
    }
}
