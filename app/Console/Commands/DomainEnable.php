<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainEnable extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:enable {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a domain';
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

        $this->startTask("Enable domain {$name}");

        if (!$domains->enable($name)) {
            return $this->errorTask($domains->error());
        }

        return $this->completeTask();
    }
}
