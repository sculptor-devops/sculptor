<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Backups;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupRun extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backups list';

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
     */
    public function handle(Backups $actions): int
    {
        $id = $this->argument('id');

        $this->startTask("Appending backup batch {$id}");

        if(!$actions->run($id)) {
            return $this->errorTask($actions->error());
        }

        return $this->completeTask();
    }

}
