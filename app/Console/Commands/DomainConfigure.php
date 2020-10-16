<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Sculptor\Agent\Actions\Domains;

class DomainConfigure extends Command
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
    protected $description = 'Configure a domain';

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
        $name = $this->argument('name');

        if ($domains->configure($name)) {
            $this->info('Done.');

            return 0;
        }

        $this->error($domains->error());

        return 1;
    }
}
