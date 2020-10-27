<?php

if (!function_exists('whoami')) {

    function whoami(): string
    {
        $processUser = posix_getpwuid(posix_geteuid());

        return $processUser['name'];
    }
}
