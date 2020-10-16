<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Contracts\Encrypt as EncryptInterface;

/**
 * @property string name
 * @property string host
 * @property string password
 * @property Database database
 */
class DatabaseUser extends Model implements Transformable, EncryptInterface
{
    use TransformableTrait;

    use Encrypt;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'database_id', 'host', 'password' ];

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
}
