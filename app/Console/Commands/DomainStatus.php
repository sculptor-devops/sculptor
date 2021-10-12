<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Agent\Enums\DomainStatusType;
/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainStatus extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:status {domain?} {status?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change domain status';
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
     * @param Workers $worker
     * @return int
     */
    public function handle(Domains $domains): int
    {
        $domain = $this->argument('domain');

        $status = $this->argument('status');

        if ($domain == null && $status == null) {
            $this->warn('WARNING! This is an emergency operation, use with precaution.');            
            $this->warn('Syntax: <<DOMAIN NAME>> <<PARAMETER>> <<STSTUS>>');
            $this->warn('Allowed values: ' . collect(DomainStatusType::toArray())->join(', '));

            return 0;
        }

        if ($this->askYesNo('This is an emergency operation, sure to continue?')) {
            $this->startTask("Chanhe {$domain} status to {$status}");

            $domains->status($domain, $status);

            return $this->completeTask();
            }

        return 1;
    }
}
