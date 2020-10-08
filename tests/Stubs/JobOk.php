<?php

namespace Tests\Stubs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Services\Queues\Traceable;

class JobOk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    private $wait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wait = 0)
    {
        $this->wait = $wait;
    }


    /**
     * @throws Exception
     */
    public function do(): void
    {
        if ($this->wait > 0) {
           sleep($this->wait);
        }
    }
}
