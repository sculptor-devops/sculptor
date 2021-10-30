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
use Sculptor\Agent\Jobs\Domains\Crontab;
use Sculptor\Agent\Jobs\Domains\Structure;
use Sculptor\Agent\Jobs\Domains\WebServer;
use Sculptor\Agent\Jobs\Domains\Worker;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainDelete implements ShouldQueue, ITraceable
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

        Logs::job()->info("Domain delete {$this->domain->name}");

        try {
            foreach (
                [
                         Worker::class,
                         Crontab::class,
                         WebServer::class,
                         Structure::class,
                     ] as $step
            ) {
                $stage = resolve($step);

                Logs::job()->debug("Domain deletion running step {$step}");

                $stage->delete($this->domain);
            }

            $this->enqueue(new DomainCron());

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);
        }
    }
}
