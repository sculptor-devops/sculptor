<?php

namespace Sculptor\Agent\Logs;

use Iterator;
use Carbon\Carbon;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class UpgradeLog implements Iterator
{
    /**
    * @var Carbon
    */
    private $start;
    /**
    * @var Carbon
    */
    private $end;
    /**
    * @var array
    */
    private $lines = [];
    /**
    * @var int
    */
    private $position = 0;

    public function __construct(array $lines = [], Carbon $start = null, Carbon $end = null)
    {
        $this->start = now();

        $this->end = now();

        $this->lines = $lines;

        if ($start) {
            $this->start = $start;
        }

        if ($end) {
            $this->end = $end;
        }
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): string
    {
        return $this->lines[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->lines[$this->position]);
    }

    public function toArray(): array
    {
        return $this->lines;
    }

    public function toString(): string
    {
        return implode(PHP_EOL, $this->lines);
    }

    public function start(): Carbon
    {
        return $this->start;
    }

    public function end(): Carbon
    {
        return $this->end;
    }
}
