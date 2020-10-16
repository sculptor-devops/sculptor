<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Exceptions\DaemonInvalidException;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Foundation\Services\Daemons as Services;
use Sculptor\Agent\Contracts\Action as ActionInterface;

class Daemons implements ActionInterface
{
    public const WEB = 'web';

    public const QUEUE = 'queue';

    public const DATABASE = 'database';

    public const REMOTE = 'remote';

    /**
     * @var array
     */
    public const SERVICES = [
        Daemons::DATABASE => [
            'mysql'
        ],
        Daemons::WEB => [
            'nginx',
            'php7.4-fpm'
        ],
        Daemons::QUEUE => [
            'redis',
            'supervisor'
        ],
        Daemons::REMOTE => [
            'ssh'
        ]
    ];

    /**
     * @var Services
     */
    private $daemons;
    /**
     * @var Action
     */
    private $action;

    public function __construct(Action $action, Services $daemons)
    {
        $this->action = $action;

        $this->daemons = $daemons;
    }

    private function valid(string $name): bool
    {
        return array_key_exists($name, Daemons::SERVICES);
    }

    public function disable(string $name): bool
    {
        return $this->operation($name, DaemonOperationsType::DISABLE, 'Disable service');
    }

    public function enable(string $name): bool
    {
        return $this->operation($name, DaemonOperationsType::ENABLE, 'Enable service');
    }

    public function restart(string $name): bool
    {
        return $this->operation($name, DaemonOperationsType::RESTART, 'Restart service');
    }

    public function start(string $name): bool
    {
        return $this->operation($name, DaemonOperationsType::START, 'Start service');
    }

    public function stop(string $name): bool
    {
        return $this->operation($name, DaemonOperationsType::STOP, 'Stop service');
    }

    public function reload(string $name): bool
    {
        return $this->operation($name, DaemonOperationsType::RELOAD, 'Reload service');
    }

    public function status(): array
    {
        $result = [];

        foreach (Daemons::SERVICES as $key => $group) {
            foreach ($group as $daemon) {
                $active = $this->daemons->active($daemon) ? 'YES' : 'NO';

                $result[] = ['group' => $key, 'name' => $daemon, 'active' => $active];
            }
        }

        return $result;
    }

    private function operation(string $name, string $operation, string $message): bool
    {
        Logs::actions()->info("{$message} group {$name}");

        try {
            if (!$this->valid($name)) {
                throw new DaemonInvalidException($name);
            }

            foreach (Daemons::SERVICES[$name] as $daemon) {
                Logs::actions()->debug("{$message} {$daemon}");

                $this->action
                    ->run(new DaemonService($daemon, $operation));
            }

            return true;
        } catch (Exception $e) {
            $this->action->report("{$message}: {$e->getMessage()}");

            return false;
        }
    }

    public function error(): ?string
    {
        return $this->action->error();
    }
}
