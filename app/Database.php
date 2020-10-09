<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'driver' ];

    public function users()
    {
        return $this->hasMany('App\DatabaseUser');
    }
}
