<?php

namespace Sculptor\Agent\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sculptor\Agent\Backup\Factory;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Queues\Traceable;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Foundation\Services\Daemons;

class BackupCreate implements ShouldQueue, ITraceable
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
     * @throws Exception
     */
    public function handle(Factory $backups): void
    {
        $this->running();

        try {
            $batch = $backups->make($this->backup);

            $batch->check($this->backup);

            $this->backup->change(BackupStatusType::RUNNING);

            if ($batch->create($this->backup)) {
                $batch->clean($this->backup);
            }

            $this->backup->change(BackupStatusType::OK);

            $this->ok();
        } catch (Exception $e) {
            $this->backup->change(BackupStatusType::ERROR, $e->getMessage());

            $this->report($e);
        }
    }
}
