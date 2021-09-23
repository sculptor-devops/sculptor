<?php

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

if (!function_exists('whoami')) {

    function whoami(): string
    {
        $processUser = posix_getpwuid(posix_geteuid());

        return $processUser['name'];
    }
}

if (!function_exists('userhome')) {

    function userhome(): string
    {
        $processUser = posix_getpwuid(posix_geteuid());

        return $processUser['home'];
    }
}

if (!function_exists('byteToHumanReadable')) {
    function byteToHumanReadable(int $size, int $precision = 2): string
    {
        $i = 0;
        $step = 1024;
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision) . $units[$i];
    }
}

if (!function_exists('composerVersion')) {
    function composerVersion(): ?string
    {
        $content = file_get_contents(__DIR__ . '/../composer.json');

        $payload = json_decode($content, true);

        if ($payload['version']) {
            return $payload['version'];
        }

        return null;
    }
}

