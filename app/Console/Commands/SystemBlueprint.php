<?php

namespace App\Console\Commands;

use Sculptor\Agent\Blueprint;
use Sculptor\Agent\Support\CommandBase;

class SystemBlueprint extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:blueprint {operation} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create system blueprint';

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
     * @param Blueprint $blueprint
     * @return int
     */
    public function handle(Blueprint $blueprint): int
    {
        $operation = $this->argument('operation');

        $file = $this->argument('file');

        $this->startTask("Blueprint {$operation}: {$file}");

        switch ($operation) {
            case 'create':
                $blueprint->create($file);

                return $this->completeTask();

            case 'load':
                $blueprint->load($file);

                return $this->completeTask();
        }

        $this->errorTask("Unknown operation {$operation}");

        return 1;
    }
}
