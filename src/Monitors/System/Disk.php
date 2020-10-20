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
            "{$this->name()}.{$device}.free" => $free,
            "{$this->name()}.{$device}.total" => $total,
            "{$this->name()}.{$device}.percent" => $percent
        ];
    }

    public function name(): string
    {
        return 'disk';
    }
}
