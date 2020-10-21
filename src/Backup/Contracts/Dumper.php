<?php namespace Sculptor\Agent\Backup\Contracts;


interface Dumper
{
    public function dump(string $filename): bool;
    public function name(): string;
}
