<?php

namespace Sculptor\Agent;

use InvalidArgumentException;
use Sculptor\Agent\Facades\Configuration;

class LookupResolver
{
    public static function array($app, string $key): array
    {
        $lookup = Configuration::getArray($key);

        array_walk($lookup, fn (&$value, $key) => $value = $app->get($value));

        return $lookup;
    }

    public static function driver($app, string $drivers, string $key)
    {
        $driver = Configuration::get($key);

        $lookup = Configuration::getArray($drivers);

        if (array_key_exists($driver, $lookup)) {
            return $app->get($lookup[$driver]);
        }

        throw new InvalidArgumentException("Invalid {$driver} rotation type");
    }
}