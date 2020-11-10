<?php

namespace App\Console\Commands;

use Exception;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Foundation\Runner\Runner;
use Symfony\Component\Process\Process;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainDeploy extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:deploy {name} {deploy?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System deploy for a domain';

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

        $command = $this->argument('deploy');

        $this->startTask("Deploy " . ($command ?? 'run'). " {$name}");

        if (!$domains->deploy($name, $command)) {
            return $this->errorTask($domains->error());
        }

        return $this->completeTask();
    }
}
