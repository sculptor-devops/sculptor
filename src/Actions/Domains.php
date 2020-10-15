<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Jobs\DomainCreate;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Queues;
use Sculptor\Agent\Repositories\DomainRepository;

class Domains extends Base
{
    /**
     * @var DomainRepository
     */
    private $domains;

    public function __construct(Queues $queues, DomainRepository $domains)
    {
        parent::__construct($queues);

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

            $this->run(new DomainCreate($domain));

            return true;
        } catch (Exception $e) {
            $this->report("Drop user: {$e->getMessage()}");

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
}
