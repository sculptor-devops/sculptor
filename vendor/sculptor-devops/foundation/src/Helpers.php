<?php
/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 * @param string|null $path
 * @return string
 */

if (!function_exists('userHome')) {

    function userHome(string $path = null): string
    {
        $user = posix_getpwuid(posix_getuid());

        if ($path == null) {
            return $user['dir'];
        }

        return "{$user['dir']}/{$path}";
    }
}

if (!function_exists('sudo')) {

    function sudo(): bool
    {
        return (posix_getuid() == 0);
    }
}

if (!function_exists('clearNewLine')) {

    function clearNewLine(string $data): string
    {
        return str_replace(array("\r", "\n"), '', $data);
    }
}

if (!function_exists('quoteContent')) {

    function quoteContent(string $data): string
    {
        if (preg_match('/"([^"]+)"/', $data, $m)) {
            return $m[1];
        }

        return $data;
    }
}

if (!function_exists('splitNewLine')) {

    /**
     * @param string $data
     * @return array<int, string>
     */
    function splitNewLine(string $data): array
    {
        $lines = preg_split("/\r\n|\n|\r/", $data);

        if (is_array($lines)) {
            return $lines;
        }

        return [];
    }
}
