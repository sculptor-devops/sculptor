<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\EventRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemEvents extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:events {limit=25} {page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show system events';
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
        $limit = (int)$this->argument('limit');

        $page = (int)$this->argument('page');

        $items = $this->limit($limit, $page - 1);

        $this->table([
            'Date/Days',
            'Tag',
            'Level',
            'Message'
        ], $items);

        $this->info("Limited to {$limit} page {$page}");

        return 0;
    }

    private function limit(int $limit, int $page): array
    {
        return $this->events
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->skip($page * $limit)
            ->map(function ($item) {
                return [
                    'created_at' => $item->created_at->format('Y-m-d H:i'),
                    'tag' => $item->tag,
                    'level' => $item->level,
                    'message' => Str::limit($item->message, 80)
                ];
            })
            ->toArray();
    }
}
