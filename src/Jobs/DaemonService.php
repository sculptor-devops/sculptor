<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Foundation\Services\Daemons;

class DaemonService implements ShouldQueue, ITraceable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traceable;

    /**
     * @var string
     */
    private $group;
    /**
     * @var string
     */
    private $operation;

    /**
     * Create a new job instance.
     *
     * @param string $group
     * @param string $operation
     */
    public function __construct(string $group, string $operation)
    {
        $this->group = $group;

        $this->operation = $operation;
    }

    /**
     * @param Daemons $daemons
     * @param Configuration $configuration
     * @throws Exception
     */
    public function handle(Daemons $daemons, Configuration $configuration): void
    {
        $this->transaction = ($this->group != DaemonGroupType::DATABASE);

        $this->running();

        try {
            if (!$this->run($daemons, $configuration)) {
                $this->error("Unable to start {$this->group}: {$daemons->error()}");

                return;
            }

            $this->ok();
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param Daemons $daemons
     * @param Configuration $configuration
     * @return bool
     * @throws Exception
     */
    private function run(Daemons $daemons, Configuration $configuration): bool
    {
        Logs::job()->info("Daemon {$this->group} {$this->operation}");

        foreach ($configuration->services($this->group) as $service) {

            if (!$this->apply($daemons, $service)) {
                throw new Exception("Unable to {$this->operation} service $service: {$daemons->error()}");
            }
        }

        return true;
    }

    private function apply(Daemons $daemons, string $service): bool
    {
        Logs::job()->debug("Daemon {$this->operation} {$service}");

        switch ($this->operation) {
            case DaemonOperationsType::START:
                return $daemons->start($service);

            case DaemonOperationsType::STOP:
                return $daemons->stop($service);

            case DaemonOperationsType::RELOAD:
                return $daemons->reload($service);

            case DaemonOperationsType::RESTART:
                return $daemons->restart($service);

            case DaemonOperationsType::ENABLE:
                return $daemons->enable($service);

            case DaemonOperationsType::DISABLE:
                return $daemons->disable($service);
        }

        throw new Exception("Unknown daemon operation {$this->operation}");
    }
}
