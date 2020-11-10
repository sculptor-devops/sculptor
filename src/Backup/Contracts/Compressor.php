<?php

namespace Sculptor\Agent\Backup\Contracts;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface Compressor
{
    public function create(string $filename): Compressor;

    public function close(): void;

    public function directory(string $name, string $path = null): Compressor;

    public function file(string $file): Compressor;

    public function extension(): string;
}
