<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Report;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Exceptions\DatabaseNotFoundException;
use Sculptor\Agent\Jobs\BackupRun;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Repositories\Entities\Backup;

class Backups implements ActionInterface
{
    use Report;

    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var BackupRepository
     */
    private $backups;
    /**
     * @var DatabaseRepository
     */
    private $databases;

    public function __construct(
        Action $action,
        BackupRepository $backups,
        DomainRepository $domains,
        DatabaseRepository $databases
    ) {
        $this->action = $action;

        $this->backups = $backups;

        $this->domains = $domains;

        $this->databases = $databases;
    }

    /**
     * @param Backup $backup
     * @param string $name
     * @throws DatabaseNotFoundException
     * @throws Exception
     */
    private function associate(Backup $backup, string $name): void
    {
        switch ($backup->type) {
            case BackupType::DATABASE:
                $database = $this->databases->byName($name);

                $backup->database()
                    ->associate($database)
                    ->save();

                break;

            case BackupType::DOMAIN:
                $domain = $this->domains->byName($name);;

                $backup->domain()
                    ->associate($domain)
                    ->save();

                break;

            default:
                throw new Exception("Invalid backup type {$backup->type}");
        }
    }

    /**
     * @param string $type
     * @param string|null $name
     * @return bool
     * @throws ValidatorException
     */
    public function create(string $type, ?string $name = null): bool
    {
        $backup = $this->backups->create(['type' => $type]);

        try {
            Logs::backup()->info("Create backup {$type} database {$name}");

            if ($name != null) {
                $this->associate($backup, $name);
            }

        } catch (Exception $e) {
            $backup->delete();

            return $this->action
                ->report("Run backup: {$e->getMessage()}");
        }

        return true;
    }

    public function delete(int $id): bool
    {
        try {
            $backup = $this->backups->byId($id);

            Logs::backup()->info("Delete backup");

            $backup->delete();
        } catch (Exception $e) {
            return $this->action
                ->report("Backup delete: {$e->getMessage()}");
        }

        return true;
    }

    public function run(int $id): bool
    {
        try {
            $backup = $this->backups->byId($id);

            Logs::backup()->info("Appending backup {$backup->name()}");

            $this->action->runAndExit(new BackupRun($backup));
        } catch (Exception $e) {
            return $this->action
                ->report("Run backup: {$e->getMessage()}");
        }

        return true;
    }

    public function setup(int $id, string $parameter, string $value): bool
    {
        try {
            $backup = $this->backups->byId($id);

            Logs::backup()->info("Setup backup {$backup->name()} {$parameter}={$value}");

            if (!in_array($parameter, ['cron', 'destination', 'rotate'])) {
                throw new Exception("Invalid backup parameter {$parameter}");
            }

            $backup->update(["{$parameter}" => $value]);
        } catch (Exception $e) {
            return $this->action
                ->report("Setup backup: {$e->getMessage()}");
        }

        return true;
    }
}
