<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Enums\QueueStatusType;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * Class Queue.
 *
 * @property string error
 * @property string status
 * @property string uuid
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
    protected $fillable = [ 'uuid', 'status', 'type', 'error' ];

    public function ok(): bool
    {
        return $this->status == QueueStatusType::OK;
    }

    public function error(): bool
    {
        return $this->status == QueueStatusType::ERROR;
    }

    public function finished(): bool
    {
        return in_array($this->status, QueueStatusType::FINISHED_STATUSES);
    }

    public function running(): bool
    {
        return $this->status == QueueStatusType::RUNNING;
    }

    public function waiting(): bool
    {
        return $this->status == QueueStatusType::WAITING;
    }
}
