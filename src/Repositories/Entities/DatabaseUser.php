<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class DatabaseUser extends Model implements Transformable
{
    use TransformableTrait;

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
        $this->attributes['password'] =  Crypt::encryptString($value);
    }

    /**
     * @return string
     */
    public function getPasswordAttribute(): string
    {
        return Crypt::decryptString($this->attributes['password']);
    }
}
