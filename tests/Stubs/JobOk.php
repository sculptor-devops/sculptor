<?php

namespace Tests\Stubs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Queues\Traceable;

class JobOk implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    private $wait;

    /**
     * Create a new job instance.
     *
     * @param int $wait
     */
    public function __construct(int $wait = 0)
    {
        $this->wait = $wait;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        try {
            if ($this->wait > 0) {
                sleep($this->wait);
            }

            $this->ok();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
