<?php

namespace Sculptor\Agent\Backup\Contracts;

interface Compressor
{
    public function create(string $filename): Compressor;

    public function close(): void;

    public function directory(string $name, string $path = null): Compressor;

    public function file(string $file): Compressor;

    public function extension(): string;
}
