<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use Sculptor\Agent\Repositories\QueueRepository;

class ShowQueueStatus extends Command
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

        dd($tasks);


        return 1;
    }
}
