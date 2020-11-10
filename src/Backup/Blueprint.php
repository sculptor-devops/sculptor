<?php

namespace Sculptor\Agent\Backup;

use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Entities\Backup as Item;
use Sculptor\Agent\Blueprint as BlueprintService;

class Blueprint implements BackupInterface
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var mixed
     */
    private $tmp;
    /**
     * @var Archive
     */
    private $archive;
    /**
     * @var Compressor
     */
    private $compressor;
    /**
     * @var Tag
     */
    private $tag;
    /**
     * @var BlueprintService
     */
    private $blueprint;
    /**
     * @var int
     */
    private $size;

    public function __construct(Configuration $configuration, BlueprintService $blueprint, Archive $archive, Compressor $compressor, Tag $tag)
    {
        $this->configuration = $configuration;

        $this->blueprint = $blueprint;

        $this->tmp = $configuration->get('sculptor.backup.temp');

        $this->archive = $archive;

        $this->compressor = $compressor;

        $this->tag = $tag->extensions('blueprint', 'yml', $this->compressor->extension());
    }

    public function create(Item $backup): bool
    {
        $this->blueprint->create($this->tag->temp('system'));

        $compressed = $this->tag->compressed('system');

        $compressor = $this->compressor
            ->create($compressed);

        $compressor->file($this->tag->temp('system'));

        $compressor->close();

        $to = $this->tag
            ->destination('system', $backup->destination);

        $compressed = $this->tag
            ->compressed('system');

        $this->size = File::size($compressed);

        $this->archive
            ->create($backup->destination)
            ->put($to, File::get($compressed));

        return true;
    }

    public function clean(Item $backup): bool
    {
        foreach (
            [
                     $backup->destination,
                     $compressed = $this->tag->compressed('blueprint')
                 ] as $file
        ) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        return true;
    }

    public function rotate(Item $backup): bool
    {
        // TODO: Implement rotate() method.
    }

    public function archives(Item $backup): array
    {
        // TODO: Implement archives() method.
    }

    public function check(Item $backup): bool
    {
        return true;
    }

    public function size(): int
    {
        return $this->size;
    }
}
