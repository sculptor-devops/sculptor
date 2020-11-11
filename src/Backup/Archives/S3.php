<?php

namespace Sculptor\Agent\Backup\Archives;

use Aws\S3\S3Client;
use Exception;
use League\Flysystem\Directory;
use League\Flysystem\File;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Handler;
use Sculptor\Agent\Backup\Contracts\Archive;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Sculptor\Agent\Configuration;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class S3 implements Archive
{
    /**
     * @var Filesystem|null
     */
    private $filesystem;

    private $path;

    public function __construct(Configuration $configuration)
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => $configuration->get('sculptor.backup.drivers.s3.key'),
                'secret' => $configuration->get('sculptor.backup.drivers.s3.secret'),
            ],
            'version' => 'latest',
            'region' => $configuration->get('sculptor.backup.drivers.s3.region'),
            'endpoint' => $configuration->get('sculptor.backup.drivers.s3.endpoint')
        ]);

        $adapter = new AwsS3Adapter($client, $configuration->get('sculptor.backup.drivers.s3.bucket'));

        $this->filesystem = new Filesystem($adapter);
    }

    public function create(string $path): Archive
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $file
     * @param $content
     * @return Archive
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
     * @return Archive
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
     * @return Directory|File|Handler|null
     */
    public function get(string $file)
    {
        return $this->filesystem->get("{$this->path}/{$file}");
    }

    public function has(string $file): bool
    {
        return $this->filesystem->has("{$this->path}/{$file}");
    }
}
