<?php

namespace Sculptor\Agent\Jobs\Daemons;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Foundation\Services\Daemons;

class Operations
{
    /**
     * @var Daemons
     */
    private $daemons;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Daemons $daemons, Configuration $configuration)
    {
        $this->daemons = $daemons;

        $this->configuration = $configuration;
    }

    /**
     * @param string $group
     * @param string $operation
     * @return bool
     * @throws Exception
     */
    public function group(string $group, string $operation): bool
    {
        foreach ($this->configuration->services($group) as $service) {

            if (!$this->run($service, $operation)) {
                throw new Exception("Unable to {$operation} service $service: {$this->daemons->error()}");
            }
        }

        return true;
    }

    /**
     * @param string $service
     * @param string $operation
     * @return bool
     * @throws Exception
     */
    public function run(string $service, string $operation): bool
    {
        Logs::job()->debug("Daemon {$operation} {$service}");

        switch ($operation) {
            case DaemonOperationsType::START:
                return $this->daemons->start($service);

            case DaemonOperationsType::STOP:
                return $this->daemons->stop($service);

            case DaemonOperationsType::RELOAD:
                return $this->daemons->reload($service);

            case DaemonOperationsType::RESTART:
                return $this->daemons->restart($service);

            case DaemonOperationsType::ENABLE:
                return $this->daemons->enable($service);

            case DaemonOperationsType::DISABLE:
                return $this->daemons->disable($service);
        }

        throw new Exception("Unknown daemon operation {$operation}");
    }
}
