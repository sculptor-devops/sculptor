<?php

namespace Sculptor\Agent\Backup\Archives;

use League\Flysystem\Filesystem;
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

    public function create(string $path): Archive
    {
        $this->path = $path;

        return $this;
    }

    public function put(string $file, $content)
    {
        if (!$this->filesystem->has($this->path)) {
            $this->filesystem->createDir($this->path);
        }

        $this->filesystem->put("{$this->path}/{$file}", $content);
    }

    public function get(string $file)
    {
        return $this->filesystem->get($file);
    }

    public function delete(string $file)
    {
        return $this->filesystem->delete($file);
    }

    public function list(string $file)
    {
        return $this->filesystem->listContents($file, true);
    }
}
