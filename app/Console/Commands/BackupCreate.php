<?php

namespace App\Console\Commands;

use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Actions\Backups;
use Sculptor\Agent\Support\CommandBase;

class BackupCreate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {type} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup batch';

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
     * @param Backups $actions
     * @return int
     * @throws ValidatorException
     */
    public function handle(Backups $actions): int
    {
        $type = $this->argument('type');

        $name = $this->argument('name');

        $this->startTask("Creating backup batch {$type} for " . ($name ?? 'none') );

        if (!$actions->create($type, $name)) {
            return $this->errorTask($actions->error());
        }

        return $this->completeTask();
    }
}
