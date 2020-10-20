<?php

namespace Sculptor\Agent\Monitors\System;

class Cpu
{
    public function values(array $configuration = []): array
    {
        return [ "{$this->name()}.load" => sys_getloadavg()[0] ];
    }

    public function name(): string
    {
        return 'cpu';
    }
}
