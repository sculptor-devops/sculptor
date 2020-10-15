<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property string type
 * @property string user
 * @property string name
 * @property string vcs
 * @property string aliases
 * @property string certificate
 * @property Database database
 * @property DatabaseUser databaseUser
 * @property int database_user_id
  */
class Domain extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'alias', 'type', 'certificate', 'user', 'home', 'deployer', 'vcs_tye', 'vcs' ];

    public function root(): string
    {
        return SITES_HOME . "/{$this->user}/sites/{$this->name}";
    }

    public function configs(): string
    {
        return "{$this->root()}/config";
    }

    /**
     * @param string $value
     */
    public function setVcsAttribute(string $value): void
    {
        $this->attributes['vcs'] =  Crypt::encryptString($value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function getVcsAttribute(string $value): string
    {
        return Crypt::decryptString($this->attributes['vcs']);
    }

    public function database(): HasOne
    {
        return $this->hasOne(Database::class);
    }

    public function databaseUser(): HasOne
    {
        return $this->hasOne(DatabaseUser::class);
    }

    public function serverNames(): string
    {
        if ($this->aliases == null) {
            return $this->name;
        }

        return "{$this->name}, {$this->aliases}";
    }
}
