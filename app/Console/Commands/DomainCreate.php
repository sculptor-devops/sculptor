<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainCreate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:create {name} {type=laravel}';

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

        $this->startTask("Create domain {$name} type {$type}");

        if (!$domains->create($name, $type)) {
            return $this->errorTask($domains->error());
        }

        $this->completeTask();

        $this->warn("Now you need to run domain:setup {$name} to make modifications");

        return 0;
    }
}
