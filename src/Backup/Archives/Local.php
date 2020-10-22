<?php

namespace Sculptor\Agent\Backup\Archives;

use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Contracts\Archive;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

class Local implements Archive
{
    /**
     * @var Filesystem|null
     */
    private $filesystem;

    public function create(string $path): Archive
    {
        $adapter = new LocalAdapter($path);

        $this->filesystem = new Filesystem($adapter);

        return $this;
    }

    /**
     * @param string $file
     * @param $content
     * @throws FileExistsException
     */
    public function put(string $file, $content)
    {
        if (!File::exists(dirname($file))) {
            File::makeDirectory(dirname($file), 0755, true);
        }

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
