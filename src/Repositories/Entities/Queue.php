<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Queue.
 *
 * @property mixed error
 * @property mixed status
 * @package namespace Sculptor\Agent\Entities;
 */
class Queue extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'uuid', 'status', 'error' ];

    public function ok(): bool
    {
        return $this->status == QUEUE_STATUS_OK;
    }

    public function error(): bool
    {
        return $this->status == QUEUE_STATUS_ERROR;
    }

    public function finished(): bool
    {
        return in_array($this->status, QUEUE_FINISHED_STATUSES);
    }

    public function running(): bool
    {
        return $this->status == QUEUE_STATUS_RUNNING;
    }

    public function waiting(): bool
    {
        return $this->status == QUEUE_STATUS_WAITING;
    }
}
