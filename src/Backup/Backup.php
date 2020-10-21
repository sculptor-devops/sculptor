<?php

namespace Sculptor\Agent\Backup;

use Sculptor\Agent\Backup\Archives\S3;
use Sculptor\Agent\Backup\Dumper\Factory;
use Exception;
use Illuminate\Support\Facades\File;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Backup
{
    public function __construct()
    {
        
    }

    public function create(Backup $backup): bool
    {

    }

    public function rotate(Backup $backup): bool
    {

    }

/*
    private $archive;


    public function __construct(S3 $archive)
    {
        $this->archive = $archive;
    }

    public function run()
    {
        $size = 0;

        foreach ($this->configuration->get('sites') as $name => $value) {
            try {
                $destination = $this->configuration->get("sites.{$name}.destination");
                $db = $this->configuration->get("sites.{$name}.assets.db");
                $files = $this->configuration->get("sites.{$name}.assets.files");

                $this->context->startTask("SITE {$name}", 'Creating...');

                $this->files($name, $files);
                $this->dump($name, $db);
                $this->move($destination, $name);

                $size += File::size($this->archive($name));
                $this->context->endTask("SITE {$name}", 'Done', true);

                File::delete($this->archive($name));
            } catch (Exception $e) {
                $this->context->endTask("SITE {$name}",false, $e->getMessage());
            }
        }

        $this->context->info("TOTAL " . hrSize($size));
    }

    private function archive($name)
    {
        $temp = $this->configuration->get('tmp');

        return "{$temp}/{$name}.zip";
    }

    private function db($name)
    {
        $temp = $this->configuration->get('tmp');

        return "{$temp}/{$name}.sql";
    }

    private function move($destination, $name)
    {
        $to = "{$destination}/{$name}.zip";

        $this->archive->put($to, File::get($this->archive($name)));
    }

    private function dump($name, $config)
    {
        if ($config == '') {
            return;
        }

        $filename = $this->db($name);
        $zip = new PkZip($this->archive($name));
        $dumper = Factory::make($config);

        $dumper->dump($filename);

        $zip->file($filename);
        $zip->close();
    }

    private function files($name, $files)
    {
	if ($files == null) {
		return;
	}

        $zip = new PkZip($this->archive($name));

        foreach ($files as $file) {
            $zip->directory($file);
        }

        $zip->close();
    }*/

}
