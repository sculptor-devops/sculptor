<?php

namespace App\Console\Commands;

use Sculptor\Agent\Monitors\System;
use Sculptor\Agent\Monitors\Formatter;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemMonitors extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:monitors {operation=show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage system monitors';

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
     * @param System $monitors
     * @param Formatter $formatter
     * @return int
     */
    public function handle(System $monitors, Formatter $formatter): int
    {
        $operation = $this->argument('operation');

        $this->startTask("Actions {$operation}");


        switch ($operation) {
            case 'reset':
                $monitors->reset();;

                return $this->completeTask();

            case 'write':
                $monitors->write();;

                return $this->completeTask();

            case 'show':
                $this->completeTask();
                
                $this->info('You can also use all, reset and write');

                $values = $this->toKeyValue($monitors->last());

                foreach ($values as &$value) {
                    $value['value'] = $formatter->value($value['key'], $value['value']);

                    $value['key'] = $formatter->name($value['key']);
                }

                $this->table(['Monitor', 'Value'], $values);

                return 0;

            case 'all':
                $this->completeTask();

                $values = collect($monitors->read());

                $headers = [];

                foreach ($this->toKeyValueHeaders($values) as $header) {
                    $headers[] = $formatter->name($header);
                }

                $this->table($headers, $values->map(function ($items) use ($formatter) {
                    foreach ($items as $key => $value) {
                        $items[$key] = $formatter->value($key, $value);
                    }

                    return $items;
                }));

                $this->info(count($values) . ' samples of ' . $monitors->rotation());

                return 0;
        }

        $this->error("Unknown operation {$operation}");

        return 1;
    }
}
