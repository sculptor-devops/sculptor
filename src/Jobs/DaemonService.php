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
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Jobs\Daemons\Operations;
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
     * @param Operations $operations
     * @throws Exception
     */
    public function handle(Operations $operations): void
    {
        $this->transaction = ($this->group != DaemonGroupType::DATABASE);

        $this->running();

        try {
            $operations->group($this->group, $this->operation);

            $this->ok();
        } catch (Exception $e) {
            $this->report($e);
        }
    }
}
