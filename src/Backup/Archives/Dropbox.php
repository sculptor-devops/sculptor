<?php

namespace Sculptor\Agent\Backup\Archives;

use Exception;
use League\Flysystem\Directory;
use League\Flysystem\File;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Handler;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Configuration;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Dropbox implements Archive
{
    /**
     * @var Filesystem|null
     */
    private $filesystem;

    private $path;

    public function __construct(Configuration $configuration)
    {
        $client = new Client($configuration->get('sculptor.backup.drivers.dropbox.key'));

        $adapter = new DropboxAdapter($client);

        $this->filesystem = new Filesystem($adapter, ['case_sensitive' => false]);
    }

    /**
     * @param string $path
     * @return $this|Archive
     */
    public function create(string $path): Archive
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $file
     * @param $content
     * @return $this|Archive
     */
    public function put(string $file, $content): Archive
    {
        if (!$this->filesystem->has($this->path)) {
            $this->filesystem->createDir($this->path);
        }

        $this->filesystem->put("{$this->path}/{$file}", $content);

        return $this;
    }

    /**
     * @param string $file
     * @return Directory|File|Handler|mixed|null
     */
    public function get(string $file)
    {
        return $this->filesystem->get("{$this->path}/{$file}");
    }

    /**
     * @param string $file
     * @return $this|Archive
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function delete(string $file): Archive
    {
        if (!$this->filesystem->delete("{$this->path}/{$file}")) {
            throw new Exception("Cannot delete file {$this->path}/{$file}");
        }

        return $this;
    }

    /**
     * @param string $file
     * @return array
     */
    public function list(string $file): array
    {
        return $this->filesystem->listContents("{$this->path}/{$file}", true);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function has(string $file): bool
    {
        return $this->filesystem->has("{$this->path}/{$file}");
    }
}
