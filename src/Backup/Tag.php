<?php

namespace Sculptor\Agent\Backup;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Configuration;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Tag
{
    /**
     * @var string
     */
    private $tmp;
    /**
     * @var string
     */
    private $tag;
    /**
     * @var string
     */
    private $extension;
    /**
     * @var string
     */
    private $compressor;
    /**
     * @var string
     */
    private $type;

    public function __construct(Configuration $configuration)
    {
        $this->tmp = $configuration->get('sculptor.backup.temp');

        $this->tag = Carbon::now()->format("Ymd-His");
    }

    private function prefix(string $name): string
    {
        return "{$this->type}-{$name}-";
    }

    public function extensions(string $type, string $extension, string $compressor): Tag
    {
        $this->type = $type;

        $this->extension = $extension;

        $this->compressor = $compressor;

        return $this;
    }

    public function temp(string $name): string
    {
        return "{$this->tmp}/{$this->type}-{$name}-{$this->tag}.{$this->extension}";
    }

    public function compressed(string $name): string
    {
        return "{$this->tmp}/{$this->type}-{$name}-{$this->tag}.{$this->compressor}";
    }

    public function destination(string $name, string $destination): string
    {
        return "{$this->type}-{$name}-{$this->tag}.{$this->compressor}";
    }

    public function match(string $name, string $compare): bool
    {
        return Str::startsWith($compare, $this->prefix($name));
    }
}
