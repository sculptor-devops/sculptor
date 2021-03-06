<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Exceptions\ActionJobRunException;
use Sculptor\Agent\Exceptions\DaemonInvalidException;
use Sculptor\Agent\Exceptions\QueueJobCreateException;
use Sculptor\Agent\Exceptions\QueueJobNotTraceableException;
use Sculptor\Agent\Exceptions\QueueJobTimeoutException;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Foundation\Services\Daemons as Services;
use Sculptor\Agent\Contracts\Action as ActionInterface;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Daemons implements ActionInterface
{
    use Actionable;

    /**
     * @var Services
     */
    private $daemons;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Action $action, Services $daemons, Configuration $configuration)
    {
        $this->action = $action;

        $this->daemons = $daemons;

        $this->configuration = $configuration;
    }

    private function valid(string $name): bool
    {
        return array_key_exists($name, $this->configuration->services());
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

        foreach ($this->configuration->services() as $key => $group) {
            foreach ($group as $daemon) {
                $active = $this->daemons->active($daemon);

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

            $this->run($name, $operation);

            return true;
        } catch (Exception $e) {
            $this->action->report("{$message}: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * @param string $daemon
     * @param string $operation
     * @throws ActionJobRunException
     * @throws QueueJobCreateException
     * @throws QueueJobNotTraceableException
     * @throws QueueJobTimeoutException
     */
    private function run(string $daemon, string $operation): void
    {
        // WORK AROUND MYSQL
        if ($daemon == config('sculptor.database.default')) {
            $this->action
                ->runAndExit(new DaemonService($daemon, $operation));

            sleep(config('sculptor.database.restart_delay', 5));

            return;
        }

        $this->action
            ->run(new DaemonService($daemon, $operation));
    }
}
