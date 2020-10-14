<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Database.
 *
 * @property int id
 * @property HasMany users
 * @package namespace Sculptor\Agent\Entities;
 */
class Database extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'driver' ];

    public function users(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }
}
