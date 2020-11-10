<?php

namespace Sculptor\Agent\Backup;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Entities\Backup as Item;

class Domain implements BackupInterface
{
    /**
     * @var Archive
     */
    private $archive;
    /**
     * @var string
     */
    private $tmp;
    /**
     * @var Compressor
     */
    private $compressor;
    /**
     * @var Tag
     */
    private $tag;
    /**
     * @var int
     */
    private $size;

    public function __construct(Configuration $configuration, Archive $archive, Compressor $compressor, Tag $tag)
    {
        $this->tmp = $configuration->get('sculptor.backup.temp');

        $this->archive = $archive;

        $this->compressor = $compressor;

        $this->tag = $tag->extensions('domain', $this->compressor->extension(), $this->compressor->extension());
    }

    private function move(string $name, string $destination): void
    {
        $to = $this->tag->destination($name, $destination);

        $compressed = $this->tag->compressed($name);

        $this->size = File::size($compressed);

        $this->archive
            ->create($destination)
            ->put($to, File::get($compressed));
    }

    /**
     * @param Item $backup
     * @return bool
     */
    public function create(Item $backup): bool
    {
        $domain = $backup->domain;

        $this->files($domain->name, [
            "{$domain->root()}/shared" => 'shared',
            "{$domain->root()}/configs" => 'configs',
            "{$domain->root()}/certs" => 'certs',
        ]);

        $this->move($domain->name, $backup->destination);

        return true;
    }

    private function files(string $name, array $files): void
    {
        $compressed = $this->tag->compressed($name);

        $compressor = $this->compressor
            ->create($compressed);

        foreach ($files as $root => $path) {
            $compressor->directory($root, $path);
        }

        $compressor->close();
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function rotate(Item $backup): bool
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param Item $backup
     * @return array
     * @throws Exception
     */
    public function archives(Item $backup): array
    {
        throw new Exception("Not implemented");
    }

    /**
     * @param Item $backup
     * @return bool
     * @throws Exception
     */
    public function check(Item $backup): bool
    {
        if ($backup->domain == null) {
            throw new Exception("Backup must define a domain");
        }

        if ($backup->destination == null) {
            throw new Exception("Backup must define a destination");
        }

        return true;
    }

    public function clean(Item $backup): bool
    {
        $compressed = $this->tag->compressed($backup->domain->name);

        $temp = $this->tag->temp($backup->domain->name);

        if (File::extension($compressed)) {
            File::delete($compressed);
        }

        if (File::extension($temp)) {
            File::delete($temp);
        }

        return true;
    }

    public function size(): int
    {
        return $this->size;
    }
}
