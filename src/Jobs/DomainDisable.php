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
use Sculptor\Agent\Jobs\Domains\WebServer;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainDisable implements ShouldQueue, ITraceable
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
     * @param WebServer $web
     * @throws Exception
     */
    public function handle(WebServer $web): void
    {
        $this->running();

        Logs::job()->info("Domain disable {$this->domain->name}");

        try {
            $web->disable($this->domain);

            DomainCron::dispatch();

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);
        }
    }
}
