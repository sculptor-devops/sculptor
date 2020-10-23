<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Sculptor\Agent\Contracts\BlueprintRecord;
use Sculptor\Agent\Support\BlueprintSerializer;

/**
 * @property string value
 * @property string name
 */
class Configuration extends Model implements Transformable, BlueprintRecord
{
    use TransformableTrait;

    use Encrypt;

    use BlueprintSerializer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value'];

    /**
     * @param string $value
     */
    public function setNameAttribute(string $value): void
    {
        $this->encrypt('name', $value);
    }

    /**
     * @return string|null
     */
    public function getNameAttribute(): ?string
    {
        return $this->decrypt('name');
    }

    /**
     * @param string $value
     */
    public function setValueAttribute(string $value): void
    {
        $this->encrypt('value', $value);
    }

    /**
     * @return string|null
     */
    public function getValueAttribute(): ?string
    {
        return $this->decrypt('value');
    }

    public function serialize(): array
    {
        return $this->serializeFiler();
    }
}
