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
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Jobs\Domains\Certificates;
use Sculptor\Agent\Jobs\Domains\Permissions;
use Sculptor\Agent\Jobs\Domains\WebServer;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainCertbot implements ShouldQueue, ITraceable
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
     * @var string
     */
    private $hook;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     * @param string $hook
     */
    public function __construct( Domain $domain, string $hook) {
        $this->domain = $domain;

        $this->hook = $hook;
    }

    /**
     * @param Certificates $certificates
     * @param WebServer $webServer
     * @param Permissions $permissions
     * @throws Exception
     */
    public function handle(Certificates $certificates, WebServer $webServer, Permissions $permissions): void
    {
        $this->running();

        Logs::job()->info("Domain certbot {$this->domain->name} {$this->hook}");

        try {
            switch ($this->hook) {
                case 'create':
                    $certificates->compile($this->domain);

                    break;

                case 'deploy':
                    $certificates->copy($this->domain);

                    $certificates->apply($this->domain);

                    $permissions->compile($this->domain);

                    $webServer->enable($this->domain);

                    break;

                    case 'pre':
                        $permissions->compile($this->domain);

                        break;
                default:
                    throw new Exception("Received invalid {$this->hook} hook");
            }

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);
        }
    }
}
