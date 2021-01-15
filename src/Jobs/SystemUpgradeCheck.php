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
use Sculptor\Agent\Jobs\Domains\Worker;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Logs\Upgrades;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Domain;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemUpgradeCheck implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param Upgrades $upgrades
     * @throws Exception
     */
    public function handle(Upgrades $upgrades): void
    {
        try {
            if (count($upgrades->events()) == 0) {
                return;
            }

            $event = $upgrades->last();

            if ($event->recent()) {
                $packages = implode(', ', $event->packages());

                Logs::security()->alert("System unattended upgrades {$packages}");
            }

        } catch (Exception | Error $e) {
            report($e);
        }
    }
}
