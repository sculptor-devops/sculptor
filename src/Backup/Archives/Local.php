<?php

namespace Sculptor\Agent\Backup\Archives;

use Eppak\Contracts\Archive;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

class Local implements Archive
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Local constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $adapter = new LocalAdapter($path);
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * @param string $file
     * @param $content
     * @throws FileExistsException
     */
    public function put(string $file, $content)
    {
        $this->filesystem->write($file, $content);
    }

    /**
     * @param string $file
     * @return bool|false|mixed|string
     * @throws FileNotFoundException
     */
    public function get(string $file)
    {
        return $this->filesystem->read($file);
    }

    /**
     * @param string $file
     * @throws FileNotFoundException
     */
    public function delete(string $file)
    {
        $this->filesystem->delete($file);
    }

    /**
     * @param string $file
     * @return array
     */
    public function list(string $file)
    {
        return $this->filesystem->listContents($file, true);
    }
}
