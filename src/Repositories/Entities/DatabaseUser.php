<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
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

    public function database()
    {
        return $this->belongsTo(Database::class);
    }

    /**
     * @param string $value
     */
    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] =  Crypt::encryptString($value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function getPasswordAttribute()
    {
        return Crypt::decryptString($this->attributes['password']);
    }
}
