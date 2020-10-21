<?php

namespace Sculptor\Agent\Monitors\System;

use Sculptor\Foundation\Contracts\Runner;

class Memory
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function values(array $configuration = []): array
    {
        $result = $this->runner->runOrFail(['free']);

        $free = trim($result);
        $free_arr = explode("\n", $free);

        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $percent = ceil($mem[2] / $mem[1] * 100);

        return [
            "{$this->name()}.total" => ceil($mem[1] * 1024),
            "{$this->name()}.used" => ceil($mem[2] * 1024),
            // "{$this->name()}.percent" => $percent
        ];
    }

    public function name(): string
    {
        return 'memory';
    }
}
