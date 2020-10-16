<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Jobs\Domains\Deployer;
use Sculptor\Agent\Jobs\Domains\Permissions;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

class DomainDeploy implements ShouldQueue, ITraceable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traceable;

    /**
     * @var Domain
     */
    private $domain;
    /**
     * @var string
     */
    private $command;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     * @param string $command
     */
    public function __construct(Domain $domain, string $command)
    {
        $this->domain = $domain;

        $this->command = $command;
    }

    /**
     * @param Deployer $deploy
     * @param Permissions $permission
     * @throws Exception
     */
    public function handle(Deployer $deploy, Permissions $permission): void
    {
        $this->running();

        Logs::job()->info("Domain deploy {$this->domain->name} command {$this->command}");

        try {
            $deploy->compile($this->domain);

            $deploy->run($this->domain, $this->command);

            $permission->run($this->domain);

            $this->ok();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
