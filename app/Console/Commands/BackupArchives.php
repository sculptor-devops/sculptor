<?php

namespace App\Console\Commands;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Lorisleiva\CronTranslator\CronParsingException;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Support\CommandBase;
use Lorisleiva\CronTranslator\CronTranslator;
use Sculptor\Agent\Backup\Factory;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupArchives extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:archives {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backups archives';

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
     * @return int
     * @throws CronParsingException
     */
    public function handle(Factory $factory, BackupRepository $backups): int
    {
        if (!$this->hasArguments()) {
            $this->warn('Syntax: backup:archives <<ID>>');
            
            return 1;
        }

        $id = $this->argument('id');

        $backup = $backups->byId($id);

        $batch = $factory->make($backup);

        $archives = collect($batch->archives($backup));

        $this->table(['File', 'Date', 'Size'], $archives->map(function($backup) {
            return [
                'name' => $backup['basename'],
                'timestamp' => new Carbon($backup['timestamp']),
                'size' => byteToHumanReadable($backup['size'])
            ];
        }));

        $count = $archives->count();

        $size = $archives->sum(function ($backup) {
            return $backup['size'];
        });

        $this->info("{$count} total, " . byteToHumanReadable($size));

        return 0;
    }
}
