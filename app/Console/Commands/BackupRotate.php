<?php

namespace App\Console\Commands;

use Lorisleiva\CronTranslator\CronParsingException;
use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Agent\Backup\Factory;
use Sculptor\Agent\Configuration;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupRotate extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:rotate {id?} {--force} {--dry}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backups archives rotation';

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
    public function handle(Factory $factory, BackupRepository $backups, Configuration $configuration): int
    {
        $id = $this->argument('id');

        $force = $this->option('force');

        $dry = $this->option('dry');

        $rotation = $configuration->get('sculptor.backup.rotation');

        if (!$this->hasArguments()) {
            $this->warn('Syntax: backup:rotate <<ID>> <<--force: without confirmation>> <<--dry: without deletion>>');

            return 1;
        }

        $backup = $backups->byId($id);

        $batch = $factory->make($backup);

        if (!$force) {
            $force = $this->askYesNo("This will purge archives using policy '{$rotation}' and limit {$backup->rotate}, continue?");
        }

        if (!$force) {
            return 1;
        }

        $purged = $batch->rotate($backup, $dry);

        $this->info("Purged " . count($purged) . ' archives');

        return 0;
    }
}
