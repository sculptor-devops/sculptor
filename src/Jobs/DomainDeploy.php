<?php

namespace Sculptor\Agent\Jobs;

use Error;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Jobs\Domains\Deployer;
use Sculptor\Agent\Jobs\Domains\Permissions;
use Sculptor\Agent\Jobs\Domains\WebServer;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\Chronometer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
     * @var string|null
     */
    private $command;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     * @param string|null $command
     */
    public function __construct(Domain $domain, ?string $command)
    {
        $this->domain = $domain;

        $this->command = $command;
    }

    /**
     * @param WebServer $web
     * @param Deployer $deploy
     * @param Permissions $permission
     * @throws Exception
     */
    public function handle(WebServer $web, Deployer $deploy, Permissions $permission): void
    {
        $stopwatch = Chronometer::start();

        $this->running();

        Logs::job()->info("Domain deploy {$this->domain->name} start");

        try {
            $deploy->run($this->domain, $this->command);

            $web->enable($this->domain);

            $permission->run($this->domain);

            $this->domain->update([ 'status' => DomainStatusType::DEPLOYED ]);

            Logs::job()->info("Domain deploy {$this->domain->name} command {$this->command} done in {$stopwatch->stop()}");

            DomainCron::dispatch();

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);

            $this->domain->update([ 'status' => DomainStatusType::ERROR ]);
        }
    }
}
