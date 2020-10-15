<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Jobs\DomainCreate;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Queues;
use Sculptor\Agent\Repositories\DomainRepository;
use Sculptor\Agent\Contracts\Action as ActionInterface;

class Domains implements ActionInterface
{
    /**
     * @var DomainRepository
     */
    private $domains;
    /**
     * @var Action
     */
    private $action;

    public function __construct(Action $action, DomainRepository $domains)
    {
        $this->action = $action;

        $this->domains = $domains;
    }

    public function create(
        string $name,
        string $aliases,
        string $type = 'laravel',
        string $certificate = 'self-signed',
        string $user = 'www'
    ): bool {

        Logs::actions()->info("Create domain {$name}");

        try {
            $domain = $this->domains->create([
                'name' => $name,
                'aliases' => $aliases,
                'type' => $type,
                'certificate' => $certificate,
                'user' => $user
            ]);

            $this->action->run(new DomainCreate($domain));

            return true;
        } catch (Exception $e) {
            $this->action->report("Drop user: {$e->getMessage()}");

            return false;
        }
    }

    public function delete(string $name): bool
    {
        return true;
    }

    public function configure(string $name): bool
    {
        return true;
    }

    public function deploy(string $name): bool
    {
        return true;
    }

    public function enable(string $name): bool
    {
        return true;
    }

    public function disable(string $name): bool
    {
        return true;
    }

    public function error(): ?string
    {
        return $this->action->error();
    }
}
