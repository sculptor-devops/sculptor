<?php

namespace Sculptor\Agent\Monitors;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Facades\Logs;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class System
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration, array $monitors)
    {
        $this->configuration = $configuration;

        $this->monitors = $monitors;
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

        return $values->take(-1 * $this->configuration->getInt('sculptor.monitors.rotate'));
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

        $disks = $this->configuration->monitors('disks');

        foreach ($this->monitors as $$monitor) {
            foreach ($disks as $device => $root) {
                $values = $monitor->values([
                    'device' => $device,
                    'root' => $root['root']
                ]);

                $sampled = $sampled->merge($values);
            }
        }

        return $sampled->merge(['ts' => time()]);
    }

    /**
     *
     */
    public function write(): void
    {
        Logs::batch()->debug("Writing system monitors values");

        $all = $this->read();

        $values = $this->values();

        $new = $all->push($values);

        Redis::set('monitors', json_encode($new->toArray()));
    }

    public function rotation(): int
    {
        return $this->configuration->getInt('sculptor.monitors.rotate');
    }
}
