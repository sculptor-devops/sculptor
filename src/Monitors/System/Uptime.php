<?php

namespace Sculptor\Agent\Monitors\System;

class Uptime
{
    public function values(array $configuration = []): array
    {
        $result = posix_times();

        return ["{$this->name()}.ticks" => $result['ticks']];
    }

    public function name(): string
    {
        return 'uptime';
    }
}
