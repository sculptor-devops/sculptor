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

class JobError implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->running();

        $this->error('Mock job error');
    }
}
