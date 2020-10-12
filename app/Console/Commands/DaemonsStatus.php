<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Sculptor\Agent\Actions\Daemons;

class DaemonsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemons:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daemons status';

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
     * @param Daemons $actions
     * @return int
     */
    public function handle(Daemons $actions)
    {
        $this->table(['group' => 'Group', 'name' => 'Service', 'active' => 'Active'], $actions->status());

        return 0;
    }
}
