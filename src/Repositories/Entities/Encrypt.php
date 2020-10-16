<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Support\Facades\Crypt;

trait Encrypt
{
    public function encrypt(string $attribute, ?string $value): void
    {
        if ($value) {
            $this->attributes[$attribute] = Crypt::encryptString($value);

            return;
        }

        $this->attributes[$attribute] = null;
    }

    public function decrypt(string $attribute): ?string
    {
        if ($this->attributes[$attribute]) {
            return Crypt::decryptString($this->attributes[$attribute]);
        }

        return null;
    }
}
