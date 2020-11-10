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
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Jobs\Domains\Deployer;
use Sculptor\Agent\Jobs\Domains\Permissions;
use Sculptor\Agent\Jobs\Domains\Structure;
use Sculptor\Agent\Jobs\Domains\WebServer;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainTemplates implements ShouldQueue, ITraceable
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
     * Create a new job instance.
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param Structure $structure
     * @throws Exception
     */
    public function handle(Structure $structure, Permissions $permissions): void
    {
        $this->running();

        Logs::job()->info("Domain templates {$this->domain->name}");

        try {
            if (!$structure->compile($this->domain)) {
                throw new Exception("Unable to apply templates to {$this->domain->name}");
            }

            if (!$permissions->compile($this->domain)) {
                throw new Exception("Unable to apply permissions to {$this->domain->name}");
            }

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);

            $this->domain->update([ 'status' => DomainStatusType::ERROR ]);
        }
    }
}
