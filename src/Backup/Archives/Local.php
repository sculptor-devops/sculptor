<?php

namespace Sculptor\Agent\Backup\Archives;

use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Contracts\Archive;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
     * @return Archive
     * @throws FileExistsException
     */
    public function put(string $file, $content): Archive
    {
        if (!File::exists(dirname($file))) {
            File::makeDirectory(dirname($file), 0755, true);
        }

        $this->filesystem->write($file, $content);

        return $this;
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
     * @return Archive
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function delete(string $file): Archive
    {
        if (!$this->filesystem->delete($file)) {
            throw new \Exception("Cannot delete file {$file}");
        }

        Return $this;
    }

    /**
     * @param string $file
     * @return array
     */
    public function list(string $file): array
    {
        return $this->filesystem->listContents($file, true);
    }

    public function has(string $file): bool
    {
        return $this->filesystem->has($file);
    }
}
