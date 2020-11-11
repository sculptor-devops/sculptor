<?php

namespace Sculptor\Agent\Backup\Archives;

use Aws\S3\S3Client;
use League\Flysystem\Directory;
use League\Flysystem\File;
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
     */
    public function put(string $file, $content)
    {
        if (!$this->filesystem->has($this->path)) {
            $this->filesystem->createDir($this->path);
        }

        $this->filesystem->put("{$this->path}/{$file}", $content);
    }

    /**
     * @param string $file
     */
    public function delete(string $file)
    {
        return $this->filesystem->delete("{$this->path}/{$file}");
    }

    /**
     * @param string $file
     * @return array
     */
    public function list(string $file)
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
}
