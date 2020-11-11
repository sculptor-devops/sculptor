<?php

namespace App\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lorisleiva\CronTranslator\CronParsingException;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Support\CommandBase;
use Lorisleiva\CronTranslator\CronTranslator;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupShow extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backups list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param BackupRepository $backups
     * @param Configuration $configuration
     * @return int
     * @throws CronParsingException
     */
    public function handle(BackupRepository $backups, Configuration $configuration): int
    {
        $all = $backups->all();

        $this->padded('Current system time is', now());

        $this->padded('Temp directory is', $configuration->get('sculptor.backup.temp'));

        $this->padded('Default compression driver is ', 'Zip');

        $this->padded('Default archive driver is ', $configuration->get('sculptor.backup.drivers.default'));

        $this->padded('Default rotation policy is ', $configuration->get('sculptor.backup.rotation'));

        $this->padded('Total backups batch', $all->count());

        $this->tabled($all);

        return 0;
    }

    /**
     * Execute the console command.
     *
     * @param Collection $all
     * @throws CronParsingException
     */
    private function tabled(Collection $all): void
    {
        $tabled = [];

        foreach ($all as $item) {
            $tabled[] = [
                'id' => $item->id,
                'type' => $item->type,
                'name' => $item->name(),
                'cron' => CronTranslator::translate($item->cron),
                'destination' => $item->destination ?? 'Not defined',
                'status' => $item->status,
                'size' => byteToHumanReadable($item->size ?? 0),
                'run' => $item->run ?? 'Never',
                'error' => "<fg=red>{$item->error}</>"
            ];
        }

        $this->table(['Index', 'Type', 'Resource', 'Cron', 'Destination', 'Status', 'Size', 'Run', 'Error'], $tabled);
    }
}
