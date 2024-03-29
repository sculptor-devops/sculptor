<?php

namespace Sculptor\Agent\Backup\Subjects;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Contracts\Compressor;
use Sculptor\Agent\Backup\Contracts\Backup as BackupInterface;
use Sculptor\Agent\Backup\Tag;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Entities\Backup as Item;
use Sculptor\Agent\Backup\Contracts\Rotation;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
    /**
     * @var Rotation
     */
    private $rotation;

    public function __construct(Configuration $configuration, Archive $archive, Compressor $compressor, Tag $tag, Rotation $rotation)
    {
        $this->tmp = $configuration->get('sculptor.backup.temp');

        $this->archive = $archive;

        $this->compressor = $compressor;

        $this->rotation = $rotation;

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
     * @param bool $dry
     * @return array
     * @throws Exception
     */
    public function rotate(Item $backup, bool $dry = false): array
    {
        $archives = $this->archives($backup);

        $purged = $this->rotation->rotate($archives, $backup->rotate, $backup->destination, $dry);

        return $purged;
    }

    /**
     * @param Item $backup
     * @return array
     * @throws Exception
     */
    public function archives(Item $backup): array
    {
        $all = $this->archive
            ->create($backup->destination)
            ->list('/');

        return collect($all)
            ->filter(fn($item) => $this->tag->match($backup->domain->name, $item['basename']))
            ->toArray();
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
