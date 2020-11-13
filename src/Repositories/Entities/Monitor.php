<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * @property string type
 * @property string $constraint
 * @property bool alarm
 * @property string message
 * @property string to
 * @property string rearm
 * @property string error
 */
class Monitor extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'type',
        'message',
        'to',
        'monitor',
        'constraint',
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
}
