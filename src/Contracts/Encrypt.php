<?php

namespace Sculptor\Agent\Contracts;

interface Encrypt
{
    function encrypt(string $attribute, string $value): void;
    function decrypt(string $attribute): ?string;
}
