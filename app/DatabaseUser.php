<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class DatabaseUser extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'database_id', 'host', 'password' ];

    /**
     * @return BelongsTo
     */
    public function database()
    {
        return $this->belongsTo('App\Database');
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
    public function getPasswordAttribute(string $value)
    {
        return Crypt::decryptString($this->attributes['password']);
    }
}
