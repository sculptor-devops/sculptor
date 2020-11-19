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
use Sculptor\Agent\Backup\Factory;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Support\Chronometer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupRun implements ShouldQueue, ITraceable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traceable;

    /**
     * @var Backup
     */
    private $backup;


    /**
     * Create a new job instance.
     *
     * @param Backup $backup
     */
    public function __construct(Backup $backup)
    {
        $this->backup = $backup;
    }

    /**
     * @param Factory $backups
     * @param Configuration $configuration
     * @throws Exception
     */
    public function handle(Factory $backups, Configuration $configuration): void
    {
        $this->running();

        $stopwatch = Chronometer::start();

        try {
            Logs::backup()->info("Running backup {$this->backup->name()}...");

            $this->check($backups, $configuration);

            $batch = $backups->make($this->backup);

            $batch->check($this->backup);

            $this->backup->change(BackupStatusType::RUNNING);

            if ($batch->create($this->backup)) {
                $this->backup->update(['size' => $batch->size() ]);

                $batch->clean($this->backup);
            }

            $this->backup->change(BackupStatusType::OK);

            Logs::backup()->info("Running backup {$this->backup->name()} done in {$stopwatch->stop()}");

            $this->ok();
        } catch (Exception | Error $e) {
            $this->report($e);

            $this->backup->change(BackupStatusType::ERROR, $e->getMessage());
        }
    }

    /**
     * @param Factory $backups
     * @param Configuration $configuration
     * @throws Exception
     */
    private function check(Factory $backups, Configuration $configuration): void
    {
        $testFile = '/.sculptor.test.' . time();

        $destination = $this->backup->destination;

        $archive = $backups->archive($this->backup->archive);

        $temp = $configuration->get('sculptor.backup.temp');

        if (!File::exists($temp)) {
            throw new Exception("Backup temp must exists");
        }

        if ($destination == null) {
            throw new Exception("Backup destination cannot be null");
        }

        if (
            !$archive->create($destination)
            ->put($testFile, time())
            ->has($testFile)
        ) {
            throw new Exception("Cannot write test file in destination {$destination}");
        }

        $archive->create($destination)
            ->delete($testFile);
    }
}
