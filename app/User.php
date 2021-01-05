<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Support\BlueprintSerializer;

class User extends Authenticatable implements BlueprintRecord
{
    use HasApiTokens, HasFactory, Notifiable, BlueprintSerializer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function serialize(): array
    {
        return $this->serializeFiler(['user_id', 'email_verified_at', 'password', 'remember_token']);
    }
}
