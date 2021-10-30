<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Actions\Backups\StatusMachine;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Actions\Support\Repository;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Exceptions\DatabaseNotFoundException;
use Sculptor\Agent\Jobs\BackupRun;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Repositories\DatabaseRepository;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Validation\Validator;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Backups implements ActionInterface
{
    use Actionable;

    use Repository;

    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var DatabaseRepository
     */
    private $databases;
    /**
     * @var StatusMachine
     */
    private $machine;

    public function __construct(
        Action $action,
        BackupRepository $backups,
        DomainRepository $domains,
        DatabaseRepository $databases,
        StatusMachine $machine
    ) {
        $this->action = $action;

        $this->repository = $backups;

        $this->domains = $domains;

        $this->databases = $databases;

        $this->machine = $machine;
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
                $domain = $this->domains->byName($name);
                ;

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
     */
    public function create(string $type, ?string $name = null): bool
    {
        try {
            $backup = $this->repository->make($type);

            Logs::backup()->info("Create backup {$type} database {$name}");

            if ($name != null) {
                $this->associate($backup, $name);
            }
        } catch (Exception $e) {
            return $this->action
                ->report("System backup: {$e->getMessage()}");
        }

        return true;
    }

    public function delete(int $id): bool
    {
        try {
            $backup = $this->repository->byId($id);

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
            $backup = $this->repository->byId($id);

            Logs::backup()->info("Appending backup {$backup->name()}");

            $this->action->runAndExit(new BackupRun($backup));
        } catch (Exception $e) {
            return $this->action
                ->report("System backup: {$e->getMessage()}");
        }

        return true;
    }

    public function setup(int $id, string $parameter, string $value): bool
    {
        try {
            $backup = $this->repository->byId($id);

            $validator = Validator::make('Backup');

            Logs::backup()->info("Setup backup {$backup->name()} {$parameter}={$value}");

            if (!$validator->validate($parameter, $value)) {
                throw new Exception($validator->error());
            }

            $backup->update(["{$parameter}" => $value]);
        } catch (Exception $e) {
            return $this->action
                ->report("Setup backup: {$e->getMessage()}");
        }

        return true;
    }

    public function check(int $id): bool
    {
        return false;
    }

    public function rotate(int $id): bool
    {
        return false;
    }
}
