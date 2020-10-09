<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Database.
 *
 * @property mixed id
 * @property mixed users
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

    public function users()
    {
        return $this->hasMany(DatabaseUser::class);
    }
}
