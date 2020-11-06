<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

class DomainTemplates extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:templates {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Overwrite domain templates in configs directory';
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
     */
    public function handle(Domains $domains): int
    {
        $domain = $this->argument('domain');

        $this->startTask("Domain templates {$domain}");

        if (!$domains->templates($domain)) {
            return $this->errorTask($domains->error());
        }


        return $this->completeTask();
    }

}
