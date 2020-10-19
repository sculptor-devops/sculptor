<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

class DomainDelete extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:delete {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a domain';
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

        if ($domains->delete($name)) {
            $this->info('Done.');

            return 0;
        }

        $this->error($domains->error());

        return 1;
    }
}
