<?php

namespace Sculptor\Agent\Repositories\Entities;

use Illuminate\Support\Facades\Crypt;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
