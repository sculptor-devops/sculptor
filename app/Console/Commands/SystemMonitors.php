<?php


namespace App\Console\Commands;


use Exception;
use Sculptor\Agent\Logs\Upgrades;
use Sculptor\Agent\Monitors\Collector;
use Sculptor\Agent\Monitors\Formatter;
use Sculptor\Agent\Support\CommandBase;

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
     * @param Collector $monitors
     * @param Formatter $formatter
     * @return int
     */
    public function handle(Collector $monitors, Formatter $formatter): int
    {
        $operation = $this->argument('operation');

        $this->startTask("Monitors {$operation}");

        switch ($operation) {
            case 'reset':
                $monitors->reset();;

                return $this->completeTask();

            case 'write':
                $monitors->write();;

                return $this->completeTask();

            case 'show':
                $this->completeTask();

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

                return 0;
        }

        $this->error("Unknown operation {$operation}");

        return 1;
    }
}
