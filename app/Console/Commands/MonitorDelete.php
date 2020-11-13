<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Monitors;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/


class MonitorDelete extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:delete {index}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete monitor';

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
     * @param Monitors $monitors
     * @return int
     */
    public function handle(Monitors $monitors): int
    {
        $index = $this->argument('index');

        $this->startTask("Delete monitor {$index}");

        if (!$monitors->delete($index)) {
            return $this->errorTask($monitors->error());
        }

        return $this->completeTask();
    }
}