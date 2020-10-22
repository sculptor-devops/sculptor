<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sculptor\Agent\Contracts\Encrypt as EncryptInterface;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Enums\DomainStatusType;

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
 * @property string deployer
 * @property string home
 * @property string alias
 * @property string install
 * @property string status
 * @property bool www
 * @property string email
 * @property bool enabled
 */
class Domain extends Model implements Transformable, EncryptInterface
{
    use TransformableTrait;

    use Encrypt;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'type',
        'status',
        'enabled',
        'www',
        'certificate',
        'user',
        'home',
        'install',
        'deployer',
        'vcs_tye',
        'vcs'
    ];

    public function root(): string
    {
        return SITES_HOME . "/{$this->user}/sites/{$this->name}";
    }

    public function home(): string
    {
        return "{$this->root()}/current/{$this->home}";
    }

    public function configs(): string
    {
        return "{$this->root()}/configs";
    }

    /**
     * @param string|null $value
     */
    public function setVcsAttribute(?string $value): void
    {
        $this->encrypt('vcs', $value);
    }

    /**
     * @return string
     */
    public function getVcsAttribute(): ?string
    {
        return $this->decrypt('vcs');
    }

    public function database(): BelongsTo
    {
        return $this->belongsTo(Database::class);
    }

    public function databaseUser(): BelongsTo
    {
        return $this->belongsTo(DatabaseUser::class);
    }

    public function serverName(): string
    {
        $name = $this->name;

        if ($this->www) {
            $name = "{$this->name} www.{$this->name}";
        }

        return $name;
    }

    public function serverNames(): string
    {
        $name = $this->serverName();

        if ($this->alias == null) {
            return $name;
        }

        return "{$name} {$this->alias}";
    }

    public function deployed(): bool
    {
        return $this->enabled && in_array($this->status, [
                DomainStatusType::DEPLOYED
            ]);
    }
}
