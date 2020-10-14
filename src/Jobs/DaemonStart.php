<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Foundation\Services\Daemons;

class DaemonStart implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new job instance.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param Daemons $daemons
     * @throws Exception
     */
    public function handle(Daemons $daemons): void
    {
        $this->running();

        try {
            if (!$daemons->start($this->name)) {
                $this->error("Unable to start {$this->name}: {$daemons->error()}");

                return;
            }

            $this->ok();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
