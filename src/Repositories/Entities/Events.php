<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Contracts\Encrypt as EncryptInterface;
use Sculptor\Agent\Support\BlueprintSerializer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

/**
 * @property string message
 * @property string tag
 * @property array context
 * @property string payload
 */
class Events extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'message',
        'tag',
        'type',
        'context',
        'payload'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'context' => 'array',
    ];
}
