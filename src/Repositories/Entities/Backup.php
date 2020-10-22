<?php

namespace Sculptor\Agent\Repositories\Entities;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Enums\BackupType;

/**
 * @property string type
 * @property Database database
 * @property string destination
 * @property Domain domain
 * @property string status
 * @property int size
 */
class Backup extends Model implements Transformable
{
    use TransformableTrait;

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

    public function change(string $status, string $error = null): void
    {
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
        }

        throw new Exception("Invalid backup typ {$this->type}");
    }
}
