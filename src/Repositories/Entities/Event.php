<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Contracts\Encrypt as EncryptInterface;
use Sculptor\Agent\Support\BlueprintSerializer;


/**
 * @property string message
 * @property string tag
 * @property array context
 * @property string payload
 * @property string level
 */
class Event extends Model implements Transformable
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
        'payload',
        'level'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'context' => 'array',
    ];
}
