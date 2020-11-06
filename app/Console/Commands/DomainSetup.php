<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

class DomainSetup extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:setup {name} {parameter} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup domain parameter';
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
     * @param Domains $domains
     * @return int
     * @throws Exception
     */
    public function handle(Domains $domains): int
    {
        $name = $this->argument('name');

        $parameter = $this->argument('parameter');

        $value = $this->argument('value');

        $this->startTask("Domain setup {$name} {$parameter}={$value}");

        if (!$domains->setup($name, $parameter, $value)) {
            return $this->errorTask("{$domains->error()} (Valid parameters: " . collect(Domains\Parameters::ALLOWED)->join(',') . ")");
        }

        $this->completeTask();

        $this->warn("Now you need to run domain:configure {$name} to apply modifications");

        return 0;
    }
}
