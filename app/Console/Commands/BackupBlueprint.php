<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Blueprint;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
     * @var Blueprint
     */
    private $blueprint;

    /**
     * Create a new command instance.
     *
     * @param Blueprint $blueprint
     */
    public function __construct(Blueprint $blueprint)
    {
        parent::__construct();

        $this->blueprint = $blueprint;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $operation = $this->argument('operation');

        $this->startTask("Blueprint {$operation}");

        switch ($operation) {
            case 'create':
                return $this->create();

            case 'load':
                return $this->load();

            case 'dry':
                return $this->dry();
        }

        $this->errorTask("Unknown operation {$operation} (create/load/dry)");

        return 1;
    }

    public function create(): int
    {
        $file = $this->argument('file');

        if (!$this->blueprint->create($file)) {
            return $this->errorTask($this->blueprint->error());
        }

        $this->completeTask();
    }

    public function load(): int
    {
        $file = $this->argument('file');

        if (!$this->blueprint->load($file)) {
            $this->errorTask($this->blueprint->error());
        }

        $this->completeTask();

        $this->commands();

        return 0;
    }

    public function dry(): int
    {
        $file = $this->argument('file');

        $this->blueprint->dry();

        if (!$this->blueprint->load($file)) {
            return $this->errorTask($this->blueprint->error());
        }

        $this->completeTask();

        $this->commands();

        return 0;
    }

    public function commands(): void
    {
        $commands = $this->blueprint->commands();

        $this->table([
            'Id',
            'Name',
            'Parameters',
            'Result'
        ], $commands);

        $this->info(count($commands) . ' commands');
    }
}
