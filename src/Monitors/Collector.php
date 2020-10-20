<?php

namespace Sculptor\Agent\Monitors;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Sculptor\Agent\Monitors\System\Cpu;
use Sculptor\Agent\Monitors\System\Disk;
use Sculptor\Agent\Monitors\System\Io;
use Sculptor\Agent\Monitors\System\Memory;
use Sculptor\Agent\Monitors\System\Uptime;

class Collector
{
    /**
     * @var Repository|Application|mixed
     */
    private $configuration;

    /**
     * @var string[]
     */
    private $system = [
        Cpu::class,
        Disk::class,
        Io::class,
        Memory::class,
        Uptime::class,
    ];

    public function __construct()
    {
        $this->configuration = config('sculptor.monitors');
    }

    /**
     *
     */
    public function reset(): void
    {
        Redis::set('monitors', null);
    }

    /**
     * @return Collection
     */
    public function read(): Collection
    {
        $stored = json_decode(Redis::get('monitors'), true);

        $values = collect($stored ?? []);

        return $values->take(-1 * $this->configuration['rotate']);
    }

    /**
     * @return array
     */
    public function last(): array
    {
        $values = $this->read();

        $value = $values->take(-1);

        if ($value->count() > 0) {
            return $value->first();
        }

        return [];
    }

    /**
     * @return Collection
     */
    public function values(): Collection
    {
        $sampled = collect([]);

        $disks = $this->configuration['disks'];

        foreach ($this->system as $reader) {
            $monitor = resolve($reader);

            foreach ($disks as $disk) {
                $values = $monitor->values($disk);

                $sampled = $sampled->merge($values);
            }
        }

        return $sampled;
    }

    /**
     *
     */
    public function write(): void
    {
        $now = time();

        $all = $this->read();

        $values = [ 'ts' => "{$now}", 'values' => $this->values()->toArray() ];

        $new = $all->push($values);

        Redis::set('monitors', json_encode($new->toArray()));
    }
}
