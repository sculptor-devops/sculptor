<?php

namespace App\Console\Commands;

use Sculptor\Agent\Actions\Workers;
use Sculptor\Agent\Support\CommandBase;

class DomainWorker extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:worker {domain} {status=enable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable domain worker';
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
    public function handle(Workers $worker): int
    {
        $domain = $this->argument('domain');

        $status = $this->argument('status');

        switch ($status) {
            case 'enable':
                $this->startTask("Enable domain worker {$domain}");

                $result = $worker->enable($domain);
                break;

            case 'disable':
                $this->startTask("Disable domain worker {$domain}");

                $result = $worker->disable($domain);
                break;

            default:
                $this->error("Invalid command {$status}");

                return 1;
        }

        if (!$result) {
            return $this->errorTask("{$worker->error()}");
        }

        return $this->completeTask();
    }
}
