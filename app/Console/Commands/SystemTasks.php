<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\QueueRepository;
use Sculptor\Agent\Support\CommandBase;

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
        $limit = (int)$this->argument('limit');

        $page = (int)$this->argument('page');

        $tasks = $queue
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->skip(($page  - 1) * $limit)
            ->get(['created_at', 'uuid', 'status', 'type', 'error'])
            ->map(function ($item) {
                return [
                    'created_at' => $item->created_at,
                    'uuid' => Str::limit($item->uuid, 25),
                    'status' => $item->status,
                    'type' => Str::afterLast($item->type, '\\'),
                    'error' => $item->error ?? 'None'
                ];
            })
            ->toArray();

        $this->table([
            'created_at' => 'Start',
            'uuid' => 'ID',
            'type' => 'Type',
            'status' => 'Status',
            'error' => 'Error'
        ], $tasks);

        $this->info("Limited to {$limit} page {$page}");

        return 0;
    }
}
