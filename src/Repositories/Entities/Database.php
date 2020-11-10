<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * Class Database.
 *
 * @property int id
 * @property DatabaseUser users
 * @property string name
 * @property string driver
 * @package namespace Sculptor\Agent\Entities;
 */
class Database extends Model implements Transformable, BlueprintRecord
{
    use TransformableTrait;

    use BlueprintSerializer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'driver'];

    public function users(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function serialize(): array
    {
        return $this->serializeFiler();
    }
}
