<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Contracts\Encrypt as EncryptInterface;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Support\BlueprintSerializer;
use Sculptor\Agent\Facades\Configuration;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
 * @property string token
 * @property int id
 * @property string provider
 * @property string branch
 * @property string engine
 */
class Domain extends Model implements Transformable, EncryptInterface, BlueprintRecord
{
    use TransformableTrait;

    use Encrypt;

    use BlueprintSerializer;

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
        'provider',
        'vcs',
        'token',
        'branch',
        'email'
    ];

    public function root(): string
    {
        return SITES_HOME . "/{$this->user}/sites/{$this->name}";
    }

    public function home(): string
    {
        return "{$this->current()}/{$this->home}";
    }

    public function current(): string
    {
        return "{$this->root()}/current";
    }

    public function configs(): string
    {
        return "{$this->root()}/configs";
    }

    public function externalId(): string
    {
        $hash = Configuration::get('sculptor.security.hash');

        $key = config('app.key');

        return hash_hmac($hash, $this->name, $key);
    }

    public function deployUrl(): string
    {
        return route('v1.webhook.deploy', [
            'hash' => $this->externalId(),
            'token' => $this->token ?? '!invalid'
        ]);
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

    public function serialize(): array
    {
        $values = $this->serializeFiler(['database_id', 'database_user_id', 'vcs_tye']);

        $values['database'] = $this->toName($this->database);

        $values['database_user'] = $this->toName($this->databaseUser);

        $files = [];

        foreach ([ "certs", "configs" ] as $path) {
            foreach (File::files("{$this->root()}/{$path}") as $file) {
                    $name = $file->getRelativePathname();

                $files ["{$path}/$name"] = base64_encode($file->getContents());
            }
        }

        $values['files'] = $files;

        return $values;
    }
}
