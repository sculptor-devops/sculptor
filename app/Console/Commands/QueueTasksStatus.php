<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\QueueRepository;

class QueueTasksStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:show {verbose=false}';

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
    public function handle(QueueRepository $queue)
    {
        $verbose = $this->argument('verbose');

        $tasks = $queue->all();

        $map = $tasks->map(function ($item) use ($verbose) {
            return [
                'created_at' => $item->created_at,
                'uuid' => $verbose ? $item->uuid : Str::limit($item->uuid, 25),
                'status' => $item->status,
                'type' => $item->type,
                'error' => $item->error ?? 'None'
            ];
        });

        $this->table([
            'created_at' => 'Start',
            'uuid' => 'ID',
            'status' => 'Status',
            'type' => 'Type',
            'error' => 'Error'
        ], $map);

        return 0;
    }
}
