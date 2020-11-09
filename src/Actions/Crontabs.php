<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Jobs\DomainCrontab;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\DomainRepository;

class Crontabs implements ActionInterface
{
    use Actionable;

    /**
     * @var DomainRepository
     */
    private $domains;

    public function __construct(
        Action $action,
        DomainRepository $domains
    ) {
        $this->action = $action;

        $this->domains = $domains;
    }

    public function update(): bool
    {
        Logs::actions()->info("Update crontabs");

        try {
            $domains = $this->domains
                ->deployed();

            $this->action
                ->run(new DomainCrontab($domains->toArray()));
        } catch (Exception $e) {
            return $this->action
                ->report("Update crontabs: {$e->getMessage()}");
        }

        return true;
    }
}
