<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Queue.
 *
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

    public function finished(): bool
    {
        return in_array($this->status, QUEUE_FINISHED_STATUSES);
    }
}
