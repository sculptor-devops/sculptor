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
