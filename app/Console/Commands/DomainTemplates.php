<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
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

        $answer = $this->ask("This operation will <fg=red>DELETE ALL</> customizations to {$domain} configs and deploy, do you want to continue? \n(type <fg=yellow>yes or y</> to continue)");

        if (Str::lower($answer) != 'yes' && Str::lower($answer) != 'y') {
            return 1;
        }

        $this->startTask("Domain templates {$domain}");

        if (!$domains->templates($domain)) {
            return $this->errorTask($domains->error());
        }

        $this->completeTask();

        $this->warn("Now you need to run domain:configure {$domain} to make modifications");

        return 0;
    }

}
