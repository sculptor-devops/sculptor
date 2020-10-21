<?php

namespace Sculptor\Agent\Actions;


use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Report;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Jobs\DomainCrontab;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\DomainRepository;

class Crontabs implements ActionInterface
{
    use Report;

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

    private function compile(): array
    {
        $tabs = [];

        $domains = $this->domains->all()->filter(function ($domain) {
            return $domain->deployed();
        });

        foreach ($domains as $domain) {
            $cron = File::get("{$domain->root()}/cron.conf");

            $tab = $tabs[$domain->user];

            $tab = "{$tab}\n# CRONTAB DOMAIN {$domain->name}\n{$cron}";

            $tabs[$domain->user] = $tab;
        }

        return $tabs;
    }

    public function update(): bool
    {
        Logs::actions()->info("Update crontabs");

        try {
            $tabs = $this->compile();

            $this->action
                ->run(new DomainCrontab($tabs));
        } catch (Exception $e) {
            return $this->action
                ->report("Update crontabs: {$e->getMessage()}");
        }

        return true;
    }
}
