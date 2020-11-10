<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Crontabs;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainCrontab extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:crontab';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update domains crontab';

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
     * @param Crontabs $crontabs
     * @return int
     */
    public function handle(Crontabs $crontabs): int
    {
        $this->startTask("Update domains crontab");

        if (!$crontabs->update()) {
            return $this->errorTask("{$crontabs->error()}");
        }

        return $this->completeTask();
    }

}
