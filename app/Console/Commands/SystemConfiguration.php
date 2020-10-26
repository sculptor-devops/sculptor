<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\CommandBase;

class SystemConfiguration extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:configuration {operation} {name?} {value?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage system configuration';

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
     * @param Configuration $configurations
     * @return int
     * @throws Exception
     */
    public function handle(Configuration $configurations): int
    {
        $operation = $this->argument('operation');

        $name = $this->argument('name');

        $value = (string)$this->argument('value');

        switch ($operation) {
            case 'show':
                $this->show($configurations);

                return 1;

            case 'get':
                if ($name == null) {
                    $this->error("Name cannot be null");

                    return 1;
                }

                $value = $configurations->get($name);

                $this->info("{$operation} {$name}");

                $this->info($this->toString($value));

                return 0;

            case 'set':
                if ($name == null || $value == null) {
                    $this->error("Name and value cannot be null");

                    return 1;
                }

                if (!is_string($value)) {
                    $this->error("Value must be a string");

                    return 1;
                }

                $this->startTask("Set {$name}={$value}");

                $configurations->set($name, $value);

                return $this->completeTask();

            case 'reset':
                $this->startTask("Deleting {$name} (env and default will be used)");

                if ($name == null) {
                    return $this->errorTask("Name cannot be null");
                }

                $configurations->reset($name);

                return $this->completeTask();

            case 'clear':
                $this->startTask("Clearing configurations (env and default will be used)");

                $configurations->clear();

                return $this->completeTask();
        }

        $this->errorTask("Invalid operation {$operation}: ger or set");

        return 1;
    }

    private function toString($value): string
    {
        if ($value == null) {
            return "<NULL>";
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        if (is_string($value)) {
            return $value;
        }

        return "Cannot be represented";
    }

    private function show(Configuration $configurations): void
    {
        $this->table(['Name', 'Value'], $this->toKeyValue($configurations->toArray()));
    }
}
