<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Backups;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupSetup extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:setup {id?} {parameter?} {value?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup backup parameter';
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
     * @param Backups $backups
     * @return int
     */
    public function handle(Backups $backups): int
    {
        $id = $this->argument('id');

        $parameter = $this->argument('parameter');

        $value = $this->argument('value');

        if (!$this->hasArguments()) {
            $this->warn('Syntax: backup:setup <<ID>> <<PARAMETER>> <<VALUE>>');
            
            return 1;
        }

        $this->startTask("backup setup {$id} {$parameter}={$value}");

        if (!$backups->setup($id, $parameter, $value)) {
            return $this->errorTask("{$backups->error()}");
        }

        return $this->completeTask();
    }

}
