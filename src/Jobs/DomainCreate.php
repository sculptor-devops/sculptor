<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Actions\Domains\Certificates;
use Sculptor\Agent\Actions\Domains\Deployer;
use Sculptor\Agent\Actions\Domains\Env;
use Sculptor\Agent\Actions\Domains\Permissions;
use Sculptor\Agent\Actions\Domains\Structure;
use Sculptor\Agent\Actions\Domains\WebServer;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Services\Daemons;

class DomainCreate implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var Domain
     */
    private $domain;
    /**
     * @var Structure
     */
    private $structure;
    /**
     * @var Certificates
     */
    private $certificates;
    /**
     * @var Env
     */
    private $env;
    /**
     * @var Deployer
     */
    private $deploy;
    /**
     * @var WebServer
     */
    private $web;
    /**
     * @var Permissions
     */
    private $permissions;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     */
    public function __construct(
        Domain $domain
    ) {
        $this->domain = $domain;

        $this->structure = new Structure($domain);

        $this->certificates = new Certificates($domain);

        $this->env = new Env($domain);

        $this->deploy = new Deployer($domain);

        $this->web = new WebServer($domain);

        $this->permissions = new Permissions($domain);
    }

    /**
     * @param Daemons $daemons
     * @throws Exception
     */
    public function handle(Daemons $daemons): void
    {
        $this->running();

        try {
            $this->structure->create();

            $this->certificates->create();

            $this->env->create();

            $this->deploy->create();

            $this->web->create();

            $this->permissions->create();

            $this->ok();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
