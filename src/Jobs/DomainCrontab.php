<?php

namespace Sculptor\Agent\Jobs;

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
     * @throws Exception
     */
    public function handle(Crontab $crontab): void
    {
        $this->running();

        Logs::job()->info("Domains crontab");

        try {

            foreach ($this->compile() as $user => $tab) {
                $filename = SCULPTOR_HOME . "/{$user}.crontab";

                File::put($filename, $tab);

                $crontab->update($filename, $user);
            }

            $this->ok();
        } catch (Exception $e) {
            $this->report($e);
        }
    }

    private function compile(): array
    {
        $tabs = [];

        foreach ($this->domains as $domain) {
            $cron = File::get("{$domain->root()}/cron.conf");

            $tab = $tabs[$domain->user];

            $tab = "{$tab}\n# CRONTAB DOMAIN {$domain->name}\n{$cron}";

            $tabs[$domain->user] = $tab;
        }

        return $tabs;
    }

}
