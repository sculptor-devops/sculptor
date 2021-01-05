<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Support\BlueprintSerializer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * @property int id
 * @property string type
 * @property string condition
 * @property bool alarm
 * @property string message
 * @property string to
 * @property string rearm
 * @property string error
 */
class Alarm extends Model implements Transformable, BlueprintRecord
{
    use TransformableTrait;

    use BlueprintSerializer;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'message',
        'to',
        'monitor',
        'condition',
        'cron',
        'error',
        'alarm',
        'alarm_at',
        'alarm_until',
        'rearm'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'alarm_at',
        'alarm_until'
    ];

    public function serialize(): array
    {
        return $this->serializeFiler(['alarm_id', 'alarm', 'alarm_at', 'alarm_until', 'error']);
    }
}
