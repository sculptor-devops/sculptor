<?php

namespace Sculptor\Agent\Backup\Compression;

use Sculptor\Agent\Backup\Archives\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Sculptor\Agent\Backup\Contracts\Compressor;

class Zip implements Compressor
{
    /**
     * @var string|null
     */
    private $filename;
    /**
     * @var Filesystem|null
     */
    private $filesystem;

    public function create(string $filename): Compressor
    {
        $this->filename = $filename;

        $this->open();

        return $this;
    }

    private function open(): void
    {
        $adapter = new ZipArchiveAdapter($this->filename);

        $this->filesystem = new Filesystem($adapter);
    }

    public function close(): void
    {
        $this->filesystem
            ->getAdapter()
            ->getArchive()
            ->close();
    }

    /**
     * @param string $name
     * @param string|null $path
     * @return Compressor
     * @throws FileNotFoundException
     */
    public function directory(string $name, string $path = null): Compressor
    {
        $local = new Local();

        $local->create($name);

        if ($path == null) {
            $path = $name;
        }

        foreach ($local->list('/') as $file) {
            if ($file['type'] == 'file') {
                $content = $local->get($file['path']);

                $this->filesystem
                    ->put("{$path}/{$file['path']}", $content);
            }
        }

        return $this;
    }

    /**
     * @param $file
     * @return Compressor
     * @throws FileNotFoundException
     */
    public function file(string $file): Compressor
    {
        $local = new Local();

        $local->create(dirname($file));

        $content = $local->get(basename($file));

        $this->filesystem
            ->put(basename($file), $content);

        return $this;
    }

    public function extension(): string
    {
        return 'zip';
    }
}
