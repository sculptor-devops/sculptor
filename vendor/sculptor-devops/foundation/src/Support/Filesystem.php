<?php

namespace Sculptor\Foundation\Support;

use Exception;
use Illuminate\Support\Facades\File;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Filesystem extends File
{
    /**
     * @throws Exception
     */
    public static function directory(string $name): void
    {
        if (File::exists($name)) {
            return;
        }

        if (!File::makeDirectory($name, 0755, true)) {
            throw new Exception("Cannot create ccd directory $name");
        }
    }

    /**
     * @throws Exception
     */
    public static function write(string $filename, string $content): void
    {
        $path = File::dirname($filename);

        if (!File::exists($path)) {
            Filesystem::directory($path);
        }

        if (!File::put($filename, $content)) {
            throw new Exception("Error writing file $filename");
        }
    }

    public static function json(string $filename): array
    {
        $content = File::get($filename);

        return json_decode($content, true);
    }

    public static function delTree(string $path): void
    {
        foreach (static::allFiles($path) as $file) {
            static::delete($file);
        }
    }
}
