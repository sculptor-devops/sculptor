<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

class DomainCreate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:create {name} {type=laravel} {certificate=self-signed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a domain';
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

        $type = $this->argument('type');

        $certificate = $this->argument('certificate');

        if ($domains->create($name, $type, $certificate)) {
            $this->info('Done.');

            return 0;
        }

        $this->error($domains->error());

        return 1;
    }
}
