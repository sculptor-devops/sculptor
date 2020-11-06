<?php

namespace Sculptor\Agent\Monitors\System;

use Exception;

class Disk
{
    public function values(array $configuration = []): array
    {
        $path = $configuration['root'];

        $device = $configuration['device'];

        try {
            return [
                "{$this->name()}.free.{$device}" => disk_free_space($path),
                "{$this->name()}.total.{$device}" => disk_total_space($path),
            ];
        } catch (Exception $e) {
            return [
                "{$this->name()}.free.{$device}" => 0,
                "{$this->name()}.total.{$device}" => 0,
            ];
        }
    }

    public function name(): string
    {
        return 'disk';
    }
}
