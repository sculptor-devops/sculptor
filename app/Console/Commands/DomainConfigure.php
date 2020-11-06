<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

class DomainConfigure extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:configure {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure a domain, this will take configs templates and compile all placeholder fields';

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
     * @throws \Exception
     */
    public function handle(Domains $domains): int
    {
        $name = $this->argument('name');

        $this->startTask("Configure domain {$name}");

        if (!$domains->configure($name)) {
            return $this->errorTask($domains->error());
        }

        $this->completeTask();

        $this->warn("Now you need to run domain:deploy {$name} to apply modifications");

        return 0;
    }
}
