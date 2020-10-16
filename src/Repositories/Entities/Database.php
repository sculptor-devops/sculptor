<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Actions\Domains;

/**
 * Class Database.
 *
 * @property int id
 * @property DatabaseUser users
 * @property string name
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

    public function domains(): BelongsTo
    {
        return $this->belongsTo(Domains::class);
    }
}
