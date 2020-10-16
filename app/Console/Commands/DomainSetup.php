<?php


namespace App\Console\Commands;


use Exception;
use Illuminate\Console\Command;
use Sculptor\Agent\Actions\Domains;

class DomainSetup extends Command
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
    protected $description = 'Run deploy for a domain';

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

        if ($domains->setup($name, $parameter, $value)) {
            $this->info('Done.');

            return 0;
        }

        $this->error('Valid parameters: ' . collect(Domains::PARAMETERS)->join(',') );

        $this->error($domains->error());

        return 1;
    }
}
