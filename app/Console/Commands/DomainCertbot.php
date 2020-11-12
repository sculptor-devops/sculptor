<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainCertbot extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:certbot {name} {hook=post}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Domain certbot hooks';

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

        $hook = $this->argument('hook');

        $this->startTask("Certbot {$name} hook {$hook}");

        if ($hook == 'pre') {

            return $this->completeTask();
        }

        if (!$domains->certbot($name, $hook)) {
            return $this->errorTask($domains->error());
        }

        return $this->completeTask();
    }
}
