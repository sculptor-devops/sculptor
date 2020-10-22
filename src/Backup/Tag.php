<?php

namespace Sculptor\Agent\Backup;


use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;

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

    public function __construct()
    {
        $this->tmp = config('sculptor.backup.temp');

        $this->tag = Carbon::now()->format("Ymd-His");
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
}
