<?php

namespace Sculptor\Agent\Monitors\System;

use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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

        $free = explode("\n", trim($result));
        $mem = explode(" ", $free[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);

        return [
            "{$this->name()}.total" => ceil($mem[1] * 1024),
            "{$this->name()}.used" => ceil($mem[2] * 1024)
        ];
    }

    public function name(): string
    {
        return 'memory';
    }
}
