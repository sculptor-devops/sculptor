<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Alarms;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/


class AlarmSetup extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alarm:setup {index} {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup an alarm';

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
     * @param Alarms $monitors
     * @return int
     */
    public function handle(Alarms $monitors): int
    {
        $index = $this->argument('index');

        $key = $this->argument('key');

        $value = $this->argument('value');

        $this->startTask("Setup monitor {$index} {$key}={$value}");

        if (!$monitors->setup($index, $key, $value)) {
            return $this->errorTask($monitors->error());
        }

        return $this->completeTask();
    }
}
