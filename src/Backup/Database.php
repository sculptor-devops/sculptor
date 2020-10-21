<?php

namespace Sculptor\Agent\Backup;

use Illuminate\Support\Facades\File;
use Sculptor\Agent\Backup\Archives\S3;
use Sculptor\Agent\Backup\Compression\PkZip;
use Sculptor\Agent\Backup\Contracts\Archive;
use Sculptor\Agent\Backup\Dumper\Factory;
use Sculptor\Agent\Repositories\Entities\Domain;

class Database
{
    /**
     * @var string
     */
    private $destination;
    /**
     * @var Archive
     */
    private $archive;
    /**
     * @var string
     */
    private $tmp;

    public function __construct()
    {
        $this->tmp = 'sculptor tmp';

        $this->destination = '';

        $this->archive = null;
    }

    public function create(Domain $domain): bool
    {
        $files = [ 'shared' ];

        $name = 'archivename';

        $this->files($name, $files);

        $this->move($name);

        return true;
    }

    private function move(string $name): void
    {
        $to = "{$this->destination}/{$name}.zip";

        $this->archive
            ->put($to, File::get($this->archive($name)));
    }

    private function archive(string $name): string
    {
        return "{$this->tmp}/{$name}.zip";
    }

    private function db(string $name): string
    {
        return "{$this->tmp}/{$name}.sql";
    }

    private function dump(string $name, array $config): void
    {
        $filename = $this->db($name);

        $dumper = Factory::make($config);

        $dumper->dump($filename);

        $zip = new PkZip($this->archive($name));

        $zip->file($filename);

        $zip->close();
    }

    private function files(string $name, array $files): void
    {
        if ($files == null) {
            return;
        }

        $zip = new PkZip($this->archive($name));

        foreach ($files as $file) {
            $zip->directory($file);
        }

        $zip->close();
    }
}
