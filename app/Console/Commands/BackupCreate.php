<?php

namespace App\Console\Commands;

use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Actions\Backups;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Agent\Enums\BackupType;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupCreate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {type?} {name?}';

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

        if ($type == null && $name == null) {
            $this->warn('Syntax: <<TYPE>> <<NAME>>');
            $this->warn('Allowed types: ' . collect(BackupType::toArray())->join(', '));
            $this->warn('Name: the database or domain, none for blueprint');

            return 1;
        }

        $this->startTask("Creating backup batch {$type} for " . ($name ?? 'none') );

        if (!$actions->create($type, $name)) {
            return $this->errorTask($actions->error());
        }

        return $this->completeTask();
    }
}
