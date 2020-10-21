<?php

namespace Sculptor\Agent\Backup\Archives;

use Aws\S3\S3Client;
use Eppak\Configuration;
use Eppak\Contracts\Archive;
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
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
	{
		$client = new S3Client([
		    'credentials' => [
		        'key'    => $configuration->get('s3.key', 'KEY'),
		        'secret' => $configuration->get('s3.secret', 'SECRET'),
		    ],
            'version' => 'latest',
		    'region' => $configuration->get('s3.region', 'REGION'),
		    'endpoint' => $configuration->get('s3.endpoint', 'https://example.com/end-point')
		]);

		$adapter = new AwsS3Adapter($client, $configuration->get('s3.bucket'));
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
