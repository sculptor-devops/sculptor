<?php

namespace App\Console\Commands;

use Sculptor\Agent\Repositories\EventRepository;
use Sculptor\Agent\Support\CommandBase;

class SystemClear extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear system events, tasks and other';
    /**
     * @var EventRepository
     */
    private $events;

    /**
     * Create a new command instance.
     *
     * @param EventRepository $events
     */
    public function __construct(EventRepository $events)
    {
        parent::__construct();

        $this->events = $events;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {


        return 0;
    }
}
