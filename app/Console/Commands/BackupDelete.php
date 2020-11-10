<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupDelete extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:delete {id}';

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
     * @param BackupRepository $backups
     * @return int
     * @throws Exception
     */
    public function handle(BackupRepository $backups): int
    {
        $id = $this->argument('id');

        $this->startTask("Deleting backup {$id}");

        try {
            $backup = $backups->byId($id);

            $backup->delete();

        } catch (Exception $e) {
            return $this->errorTask($e->getMessage());
        }

        return $this->completeTask();
    }
}
