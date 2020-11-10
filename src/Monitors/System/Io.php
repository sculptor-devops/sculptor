<?php

namespace Sculptor\Agent\Monitors\System;

use Exception;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Io
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param array $configuration
     * @return array
     * @throws Exception
     */
    public function values(array $configuration = []): array
    {
        $device = $configuration['device'];

        $result = $this->runner->runOrFail(["iostat", "-o", "JSON"]);

        $payload = json_decode($result, true);

        $stats = $payload['sysstat']['hosts'][0]['statistics'];

        foreach ($stats[0]['disk'] as $disk) {
            if ($disk['disk_device'] == $device) {
                return [
                    "{$this->name()}.tps.{$device}" => $disk['tps'],
                    "{$this->name()}.kbreads.{$device}" => $disk["kB_read/s"],
                    "{$this->name()}.kbwrtns.{$device}" => $disk["kB_wrtn/s"],
                ];
            }
        }

        return [
            "{$this->name()}.tps.{$device}" => 0,
            "{$this->name()}.kbreads.{$device}" => 0,
            "{$this->name()}.kbwrtns.{$device}" => 0,
        ];
    }

    public function name(): string
    {
        return 'io';
    }
}
