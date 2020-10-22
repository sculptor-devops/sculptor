<?php

namespace App\Console\Commands;

use Sculptor\Agent\Repositories\BackupRepository;
use Sculptor\Agent\Support\CommandBase;

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
     * @return int
     */
    public function handle(BackupRepository $backups): int
    {
        $tabled = [];

        $all = $backups->all();

        foreach ($all as $item) {
            $tabled[] = [
                'id' => $item->id,
                'type' => $item->type,
                'name' => $item->name(),
                'cron' => $item->cron,
                'destination' => $item->destination ?? 'Not defined',
                'status' => $item->status,
                'size' => $item->size ?? 'none',
                'run' => $item->run ?? 'Never',
                'error' => $item->error
            ];
        }

        $this->table([ 'Index', 'Type', 'Resource', 'Cron', 'Destination', 'Status', 'Size', 'Run', 'Error'], $tabled);

        $this->info("Temp directory is " . config('sculptor.backup.temp'));

        $this->info("Archive driver is Zip");

        $this->info("Compression with " . config('sculptor.backup.drivers.default'));

        return 0;
    }
}
