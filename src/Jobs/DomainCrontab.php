<?php

namespace Sculptor\Agent\Jobs;

use Error;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Jobs\Domains\Crontab;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\DomainRepository;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainCrontab implements ShouldQueue, ITraceable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traceable;

    /**
     * @var array
     */
    private $domains;

    /**
     * Create a new job instance.
     *
     * @param array $domains
     */
    public function __construct(
        array $domains
    ) {
        $this->domains = $domains;
    }

    /**
     * @param Crontab $crontab
     * @param DomainRepository $domains
     * @throws Exception
     */
    public function handle(Crontab $crontab, DomainRepository $domains): void
    {
        $this->running();

        Logs::job()->info("Domains crontab");

        try {
            foreach ($this->compile($domains) as $user => $tab) {
                $filename = SCULPTOR_HOME . "/{$user}.crontab";

                Logs::job()->debug("Creating crontab {$filename}");

                if (!File::put($filename, $tab)) {
                    throw new Exception("Cannot write crontab file {$filename}");
                }

                $crontab->update($filename, $user);
            }

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);
        }
    }

    /**
     * @param DomainRepository $domains
     * @return array
     * @throws Exception
     */
    private function compile(DomainRepository $domains): array
    {
        $tabs = [];

        foreach ($this->domains as $domain) {
            $domain = $domains->byName($domain['name']);

            $cron = File::get("{$domain->root()}/cron.conf");

            if (!array_key_exists($domain->user, $tabs)) {
                $tabs[$domain->user] = '';
            }

            $tab = $tabs[$domain->user];

            $tab = "{$tab}\n# CRONTAB DOMAIN {$domain->name}\n{$cron}";

            $tabs[$domain->user] = $tab;
        }

        return $tabs;
    }
}
