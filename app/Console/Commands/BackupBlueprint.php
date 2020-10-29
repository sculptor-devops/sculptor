<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Blueprint;
use Sculptor\Agent\Support\CommandBase;

class BackupBlueprint extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:blueprint {operation} {file}';

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
     * @throws Exception
     */
    public function handle(Blueprint $blueprint): int
    {
        $operation = $this->argument('operation');

        $file = $this->argument('file');

        $this->startTask("Blueprint {$operation}: {$file}");

        switch ($operation) {
            case 'create':
                if (!$blueprint->create($file)) {
                    $this->errorTask($blueprint->error());
                }

                return $this->completeTask();

            case 'load':
                if (!$blueprint->load($file)) {
                    $this->errorTask($blueprint->error());
                }

                $this->completeTask();

                $commands = $blueprint->commands();

                $this->table([
                    'Id',
                    'Name',
                    'Parameters',
                    'Result'
                ], $commands);

                $this->info(count($commands) . ' commands');

                return 0;
        }

        $this->errorTask("Unknown operation {$operation}");

        return 1;
    }
}
