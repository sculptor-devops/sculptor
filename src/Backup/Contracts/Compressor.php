<?php

namespace Sculptor\Agent\Backup\Contracts;


interface Compressor
{
    public function __construct(string $filename);

    public function close(): void;

    public function directory(string $name): void;

    public function file(string $file): void;
}
