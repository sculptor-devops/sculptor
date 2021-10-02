<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Enums\QueueStatusType;
use Sculptor\Agent\Repositories\QueueRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemTasks extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:tasks {limit=25} {page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param QueueRepository $queue
     * @return int
     */
    public function handle(QueueRepository $queue): int
    {
        $limit = $this->argument('limit');

        $page = $this->argument('page');

        $tasks = $queue
            ->orderBy('created_at', 'desc')
            ->limit((int)$limit)
            ->skip(((int)$page - 1) * $limit)              
            ->map(function ($item) {
                return [
                    'created_at' => $item->created_at,
                    'uuid' => Str::limit($item->uuid, 15),
                    'status' => $this->status($item->status),
                    'type' => Str::afterLast($item->type, '\\'),
                    'error' => '<error>' . Str::limit($item->error, 60) . '</error>' ?? '-'
                ];
            })
            ->toArray();

        $this->table([
            'created_at' => 'Start',
            'uuid' => 'ID',
            'status' => 'Status',
            'type' => 'Type',
            'error' => 'Error'
        ], $tasks);

        $this->info("Limited to {$limit} page {$page}");

        return 0;
    }

    private function status(string $status): string
    {
        switch ($status) {
            case QueueStatusType::ERROR:
                return '<error>' . QueueStatusType::ERROR . '</error>';

            case QueueStatusType::OK:
                return '<info>' . QueueStatusType::OK . '</info>';

            default:
                return "<fg=yellow>{$status}</>";
        }
    }
}
