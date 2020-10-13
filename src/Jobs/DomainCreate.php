<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Actions\Domains\Certificates;
use Sculptor\Agent\Actions\Domains\Structure;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Foundation\Services\Daemons;

class DomainCreate implements ShouldQueue, ITraceable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Traceable;

    /**
     * @var Structure
     */
    private $structure;
    /**
     * @var Certificates
     */
    private $certificates;

    /**
     * Create a new job instance.
     *
     * @param string $name
     * @param string $aliases
     * @param string $type
     * @param string $certificate
     * @param string $user
     */
    public function __construct(
        string $name,
        string $aliases,
        string $type,
        string $certificate,
        string $user
    ) {
        $this->structure = new Structure($name);

        $this->certificates = new Certificates($name, $aliases, $certificate);



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

            $this->env();

            $this->deployer();

            $this->web();

            $this->permissions();

            $this->ok();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function env(): void
    {

    }

    private function deployer(): void
    {

    }

    private function web(): void
    {

    }

    private function permissions(): void
    {

    }
}
