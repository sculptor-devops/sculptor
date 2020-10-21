<?php

namespace Sculptor\Agent\Monitors\System;

class Disk
{
    public function values(array $configuration = []): array
    {
        $path = $configuration['root'];

        $device = $configuration['device'];

        $free = disk_free_space($path);
        $total = disk_total_space($path);
        $percent = (100 - ceil(($free / $total) * 100));

        return [
            "{$this->name()}.free.{$device}" => $free,
            "{$this->name()}.total.{$device}" => $total,
            // "{$this->name()}.percent.{$device}" => $percent
        ];
    }

    public function name(): string
    {
        return 'disk';
    }
}
