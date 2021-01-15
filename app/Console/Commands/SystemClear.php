<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\Criteria\OlderRecords;
use Sculptor\Agent\Repositories\EventRepository;
use Sculptor\Agent\Repositories\QueueRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
        foreach ([ QueueRepository::class, EventRepository::class ] as $class) {
            $deleted = 0;

            $repository = resolve($class);

            $repository->pushCriteria(OlderRecords::class);

            foreach ($repository->all() as $record) {
                $deleted++;

                $record->delete();
            }

            $message = "Cleaned {$deleted} from " . Str::afterLast($class, "\\") ;

            if ($deleted > 0) {
                Logs::batch()->notice($message);
            }

            $this->warn($message);
        }

        return 0;
    }
}
