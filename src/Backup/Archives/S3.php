<?php

namespace Sculptor\Agent\Backup\Archives;

use Aws\S3\S3Client;
use Sculptor\Agent\Backup\Contracts\Archive;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class S3 implements Archive
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * S3 constructor.
     */
    public function __construct()
	{
		$client = new S3Client([
		    'credentials' => [
		        'key'    => config('sculptor.backup.drivers.s3.key'),
		        'secret' => config('sculptor.backup.drivers.s3.secret'),
		    ],
            'version' => 'latest',
		    'region' => config('sculptor.backup.drivers.s3.region'),
		    'endpoint' => config('sculptor.backup.drivers.s3.endpoint')
		]);

		$adapter = new AwsS3Adapter($client, config('sculptor.backup.drivers.s3.bucket'));

		$this->filesystem = new Filesystem($adapter);
	}

    /**
     * @param string $file
     * @param $content
     */
    public function put(string $file, $content)
    {
        $this->filesystem->put($file, $content);
    }

    /**
     * @param string $file
     */
    public function delete(string $file)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param string $file
     */
    public function list(string $file)
    {
        // TODO: Implement list() method.
    }

    /**
     * @param string $file
     */
    public function get(string $file)
    {
        // TODO: Implement get() method.
    }
}
