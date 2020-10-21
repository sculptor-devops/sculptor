<?php

namespace Sculptor\Agent\Backup;

use Sculptor\Agent\Backup\Archives\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class PkZip
{
    /**
     * @var
     */
    private $filename;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * PkZip constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->open();
    }

    /**
     *
     */
    private function open()
    {
        $adapter = new ZipArchiveAdapter($this->filename);
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     *
     */
    public function close()
    {
        $this->filesystem->getAdapter()->getArchive()->close();
    }

    /**
     * @param $name
     * @throws FileNotFoundException
     */
    public function directory($name)
    {
        $local = new Local($name);

        foreach ($local->list('/') as $file) {
            if($file['type'] == 'file') {
                $content = $local->get($file['path']);
                $this->filesystem->put($file['path'], $content);
            }
        }
    }

    /**
     * @param $file
     * @throws FileNotFoundException
     */
    public function file($file)
    {
        $local = new Local(dirname($file));

        $content = $local->get(basename($file));
        $this->filesystem->put(basename($file), $content);
    }
}
