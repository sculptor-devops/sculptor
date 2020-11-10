<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Criteria\OlderRecords;
use Sculptor\Agent\Repositories\EventRepository;
use Sculptor\Agent\Repositories\QueueRepository;
use Sculptor\Agent\Support\CommandBase;
use Throwable;

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
     * @param QueueRepository $tasks
     * @param EventRepository $events
     * @return int
     */
    public function handle(QueueRepository $tasks, EventRepository $events): int
    {
        foreach ([ QueueRepository::class, EventRepository::class ] as $class) {
            $deleted = 0;

            $repository = resolve($class);

            $repository->pushCriteria(OlderRecords::class);

            foreach ($repository->all() as $record) {
                $deleted++;

                $record->delete();
            }

            $message = "Cleaned {$deleted} from " . Str::afterLast($class, "\\") ;
            
            $this->warn($message);

            Logs::batch()->notice($message);
        }

        return 0;
    }
}
