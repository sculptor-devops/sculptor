<?php

namespace Sculptor\Agent\Repositories\Entities;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Support\BlueprintSerializer;

/**
 * @property string type
 * @property Database database
 * @property string destination
 * @property Domain domain
 * @property string status
 * @property int size
 * @property int rotate
 * @property string cron
 * @property string path
 */
class Backup extends Model implements Transformable, BlueprintRecord
{
    use TransformableTrait;

    use BlueprintSerializer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'cron', 'path', 'destination', 'status', 'error', 'run', 'rotate', 'size'];

    public function database(): BelongsTo
    {
        return $this->belongsTo(Database::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * @param string $status
     * @param string|null $error
     * @throws Exception
     */
    public function change(string $status, string $error = null): void
    {
        if ($this->status == BackupStatusType::RUNNING && $status == BackupStatusType::RUNNING) {
            throw new Exception("Backup {$this->name()} already running");
        }

        $this->update(['status' => $status, 'error' => $error, 'run' => now()]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function name(): string
    {
        switch ($this->type) {
            case BackupType::DOMAIN:
                return $this->domain
                    ->name;

            case BackupType::DATABASE:
                return $this->database
                    ->name;

            case BackupType::BLUEPRINT:
                return 'system';
        }

        throw new Exception("Invalid backup typ {$this->type}");
    }

    public function serialize(): array
    {
        $values = $this->serializeFiler(['database_id', 'domain_id']);

        $values['database'] = $this->toName($this->database);

        $values['domain'] = $this->toName($this->domain);

        return $values;
    }
}
