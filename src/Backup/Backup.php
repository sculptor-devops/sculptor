<?php

namespace Sculptor\Agent\Backup;

use Exception;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Backup as Item;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Backup implements BackupInterface
{
    /**
     * @var Database
     */
    private $database;
    /**
     * @var Domain
     */
    private $domain;

    public function __construct(Database $database, Domain $domain)
    {
        $this->database = $database;

        $this->domain = $domain;
    }

    /**
     * @param Item $backup
     * @return BackupInterface
     * @throws Exception
     */
    private function resolve(Item $backup): BackupInterface
    {
        switch ($backup->type) {
            case BackupType::DATABASE:
                return $this->database;

            case BackupType::DOMAIN:
                return $this->domain;
        }

        throw new Exception("Invalid backup type {$backup->type}");
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function create(Item $backup): bool
    {
        try {
            Logs::backup()->info("Backup {$backup->name()} create...");

            $resolved = $this->resolve($backup);

            $resolved->check($backup);

            if ($resolved->create($backup)) {
                $this->clean($backup);
            }

            Logs::backup()->info("Backup {$backup->name()} created");
        } catch (Exception $e) {
            Logs::backup()->error("Backup {$backup->name()} error: {$e->getMessage()}");

            Logs::backup()->report($e);

            $backup->change(BackupStatusType::ERROR, $e->getMessage());

            return false;
        }

        $backup->change(BackupStatusType::OK);

        return true;
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function rotate(Item $backup): bool
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param Item $backup
     * @return array
     * @throws Exception
     */
    public function archives(Item $backup): array
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function check(Item $backup): bool
    {
        throw new Exception("Not implemented");
    }

    public function clean(Item $backup): bool
    {
        try {
            Logs::backup()->debug("Cleaning backup temp {$backup->name()}");

            $resolved = $this->resolve($backup);

            return $resolved->clean($backup);
        } catch (Exception $e) {
            Logs::backup()->report($e);

            return false;
        }
    }
}
