<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Actions\Support\Action;
use Sculptor\Agent\Actions\Support\Actionable;
use Sculptor\Agent\Contracts\Action as ActionInterface;
use Sculptor\Agent\Jobs\DomainCron;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Repositories\DomainRepository;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Cron implements ActionInterface
{
    use Actionable;

    /**
     * @var DomainRepository
     */
    private $domains;

    public function __construct(
        Action $action,
        DomainRepository $domains
    )
    {
        $this->action = $action;

        $this->domains = $domains;
    }

    public function update(): bool
    {
        Logs::actions()->info("Update cron");

        try {
            $this->action
                ->run(new DomainCron());
        } catch (Exception $e) {
            return $this->action
                ->report("Update cron: {$e->getMessage()}");
        }

        return true;
    }
}
