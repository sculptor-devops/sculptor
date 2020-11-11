<?php

namespace Sculptor\Agent\Monitors;

use Carbon\Carbon;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Formatter
{
    private $formatters = [
        'memory.used' => 'toByteHr',
        'memory.total' => 'toByteHr',

        'disk.free' => 'toByteHr',
        'disk.total' => 'toByteHr',

        'io.kbreads' => 'iostat',
        'io.kbwrtns' => 'iostat',

        'ts' => 'ts',
        'uptime.ticks' => 'uptime'
    ];

    public function name(string $value): string
    {
        $parts = explode('.', $value);

        if (count($parts) == 3) {
            return __("sculptor.{$parts[0]}.{$parts[1]}", ['name' => $parts[2]]);
        }

        return __("sculptor.{$value}");
    }

    public function value(string $name, string $value): string
    {
        $parts = explode('.', $name);

        if (count($parts) > 1) {
            $name = "{$parts[0]}.{$parts[1]}";
        }

        if (!array_key_exists($name, $this->formatters)) {
            return $value;
        }

        $formatter = $this->formatters[$name];

        switch ($formatter) {
            case 'toByteHr':
                return byteToHumanReadable($value, 0);

            case 'uptime':
                return $this->uptime($value);

            case 'ts':
                return Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');

            case 'iostat':
                return "{$value} kB";
        }

        return $value;
    }

    private function uptime(int $value): string
    {
        $num = intval($value / 60);
        $minutes = $num % 60;
        $num = (int)($num / 60);
        $hours = $num % 24;
        $num = (int)($num / 24);
        $days = $num;

        return ("$days d $hours h $minutes m");
    }
}
