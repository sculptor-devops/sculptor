<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property string name
 * @property string host
 * @property string password
 * @property Database database
 */
class DatabaseUser extends Model implements Transformable, EncryptInterface, BlueprintRecord
{
    use TransformableTrait;

    use Encrypt;

    use BlueprintSerializer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'database_id', 'host', 'password'];

    public function database(): BelongsTo
    {
        return $this->belongsTo(Database::class);
    }

    /**
     * @param string $value
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->encrypt('password', $value);
    }

    /**
     * @return string|null
     */
    public function getPasswordAttribute(): ?string
    {
        return $this->decrypt('password');
    }

    public function serialize(): array
    {
        $values = $this->serializeFiler([ 'database_id' ]);

        $values['database'] = $this->toName($this->database);

        return $values;
    }
}
