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
                    "{$this->name()}.{$device}.tps" => $disk['tps'],
                    "{$this->name()}.{$device}.kbreads" => $disk["kB_read/s"],
                    "{$this->name()}.{$device}.kbwrtns" => $disk["kB_wrtn/s"],
                    "{$this->name()}.{$device}.kbread" => $disk['kB_read'],
                    "{$this->name()}.{$device}.kbwrtn" => $disk['kB_wrtn']
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