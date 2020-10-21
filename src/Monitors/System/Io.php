<?php

namespace Sculptor\Agent\Monitors\System;

use Exception;
use Sculptor\Foundation\Contracts\Runner;

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
                    // "{$this->name()}.kbread.{$device}" => $disk['kB_read'],
                    // "{$this->name()}.kbwrtn.{$device}" => $disk['kB_wrtn']
                ];
            }
        }

        throw new Exception("Cannot find device {$device}");
    }

    public function name(): string
    {
        return 'io';
    }
}
