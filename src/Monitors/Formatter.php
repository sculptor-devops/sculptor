<?php

namespace Sculptor\Agent\Monitors;

use Carbon\Carbon;

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
                return $this->toByteHr($value, 0);

            case 'uptime':
                return $this->uptime($value);

            case 'ts':
                return Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');

            case 'iostat':
                return "{$value} kB";
        }

        return $value;
    }

    private function toByteHr(int $size, int $precision = 2): string
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

    private function uptime(int $value): string
    {
        $num = intval($value/ 60);
        $minutes = $num % 60;
        $num = (int)($num / 60);
        $hours = $num % 24;
        $num = (int)($num / 24);
        $days = $num;

        return ("$days d $hours h $minutes m");
    }
}
