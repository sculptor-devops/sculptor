<?php

namespace Sculptor\Agent\Contracts;

interface Encrypt
{
    public function encrypt(string $attribute, string $value): void;
    public function decrypt(string $attribute): ?string;
}
