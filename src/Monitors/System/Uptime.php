<?php

namespace Sculptor\Agent\Monitors\System;

class Uptime
{
    public function values(array $configuration = []): array
    {
        // $result = posix_times();

        $uptime = @file_get_contents( "/proc/uptime");

        return ["{$this->name()}.ticks" => explode(" ",$uptime)[0] /*$result['ticks']*/];
    }

    public function name(): string
    {
        return 'uptime';
    }
}
