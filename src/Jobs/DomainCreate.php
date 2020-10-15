<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Jobs\Domains\Certificates;
use Sculptor\Agent\Jobs\Domains\Crontab;
use Sculptor\Agent\Jobs\Domains\Deployer;
use Sculptor\Agent\Jobs\Domains\Env;
use Sculptor\Agent\Jobs\Domains\Permissions;
use Sculptor\Agent\Jobs\Domains\Structure;
use Sculptor\Agent\Jobs\Domains\WebServer;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Jobs\Domains\Worker;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

class DomainCreate implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var Domain
     */
    private $domain;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     */
    public function __construct(
        Domain $domain
    ) {
        $this->domain = $domain;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->running();

        try {
            foreach ([
                         Structure::class,
                         Certificates::class,
                         Env::class,
                         Deployer::class,
                         WebServer::class,
                         Permissions::class
                     ] as $step) {

                $stage = resolve($step);

                $stage->run($this->domain);
            }

            $this->ok();

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
