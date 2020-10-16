<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    private $name;
    /**
     * @var string
     */
    private $operation;

    /**
     * Create a new job instance.
     *
     * @param string $name
     * @param string $operation
     */
    public function __construct(string $name, string $operation)
    {
        $this->name = $name;

        $this->operation = $operation;
    }

    /**
     * @param Daemons $daemons
     * @throws Exception
     */
    public function handle(Daemons $daemons): void
    {
        $this->running();

        try {
            if (!$this->run($daemons)) {
                $this->error("Unable to start {$this->name}: {$daemons->error()}");

                return;
            }

            $this->ok();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param Daemons $daemons
     * @return bool
     * @throws Exception
     */
    private function run(Daemons $daemons): bool
    {
        Logs::job()->info("Daemon domain {$this->name} {$this->operation}");

        switch ($this->operation) {
            case DaemonOperationsType::START:
                return $daemons->start($this->name);

            case DaemonOperationsType::STOP:
                return $daemons->stop($this->name);

            case DaemonOperationsType::RELOAD:
                return $daemons->reload($this->name);

            case DaemonOperationsType::RESTART:
                return $daemons->restart($this->name);

            case DaemonOperationsType::ENABLE:
                return $daemons->enable($this->name);

            case DaemonOperationsType::DISABLE:
                return $daemons->disable($this->name);
        }

        throw new Exception("Unknown daemon operation {$this->operation}");
    }
}
