<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Exceptions\DaemonInvalidException;
use Sculptor\Agent\Jobs\DaemonDisable;
use Sculptor\Agent\Jobs\DaemonEnable;
use Sculptor\Agent\Jobs\DaemonReload;
use Sculptor\Agent\Jobs\DaemonRestart;
use Sculptor\Agent\Jobs\DaemonStart;
use Sculptor\Agent\Jobs\DaemonStop;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Queues;

class Daemons extends Base
{
    public const SERVICES = [
        'database' => [
            'mysql'
        ],
        'web' => [
            'nginx',
            'php7.4-fpm'
        ],
        'queue' => [
            'redis',
            'supervisor'
        ],
        'remote' => [
            'ssh'
        ]
    ];
    /**
     * @var \Sculptor\Foundation\Services\Daemons
     */
    private $daemons;

    public function __construct(Queues $queues, \Sculptor\Foundation\Services\Daemons $daemons)
    {
        parent::__construct($queues);

        $this->daemons = $daemons;
    }

    private function valid(string $name): bool
    {
        return array_key_exists($name, Daemons::SERVICES);
    }

    public function disable(string $name): bool
    {
        return $this->operation(DaemonDisable::class, $name, 'Disable service');
    }

    public function enable(string $name): bool
    {
        return $this->operation(DaemonEnable::class, $name, 'Enable service');
    }

    public function restart(string $name): bool
    {
        return $this->operation(DaemonRestart::class, $name, 'Restart service');
    }

    public function start(string $name): bool
    {
        return $this->operation(DaemonStart::class, $name, 'Start service');
    }

    public function stop(string $name): bool
    {
        return $this->operation(DaemonStop::class, $name, 'Stop service');
    }

    public function reload(string $name): bool
    {
        return $this->operation(DaemonReload::class, $name, 'Reload service');
    }

    public function status(): array
    {
        $result = [];

        foreach (Daemons::SERVICES as $key => $group) {
            foreach ($group as $daemon) {
                $active = $this->daemons->active($daemon) ? 'YES' : 'NO';

                $result[] = ['group' => $key, 'name' => $daemon, 'active' => $active ];
            }
        }

        return $result;
    }

    private function operation(string $job, string $name, string $message): bool
    {
        Logs::actions()->info("{$message} group {$name}");

        try {
            if (!$this->valid($name)) {
                throw new DaemonInvalidException($name);
            }

            foreach (Daemons::SERVICES[$name] as $daemon) {
                Logs::actions()->info("{$message} {$daemon}");

                $this->run(new $job($daemon));
            }

            return true;
        } catch (Exception $e) {
            $this->report("{$message}: {$e->getMessage()}");

            return false;
        }
    }
}
