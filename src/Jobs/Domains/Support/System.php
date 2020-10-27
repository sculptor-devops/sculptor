<?php

namespace Sculptor\Agent\Jobs\Domains\Support;

use Illuminate\Support\Facades\File;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Enums\DaemonOperationsType;
use Sculptor\Agent\Jobs\DaemonService;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Foundation\Contracts\Runner;

class System
{
    /**
     * @var System
     */
    private $runner;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Runner $runner, Configuration $configuration)
    {
        $this->runner = $runner;

        $this->configuration = $configuration;
    }

    public function runAs(string $from, string $identity, array $command, int $timeout = null): void
    {
        $command = array_merge(['sudo', '-u', $identity], $command);

        $this->run($from, $command, $timeout);
    }

    public function run(string $from, array $command, int $timeout = null): void
    {
        $this->runner
            ->timeout($timeout)
            ->from($from)
            ->runOrFail($command);
    }

    public function deleteIfExists(string $filename): void
    {
        if (!File::exists($filename)) {
            return;
        }

        if (!File::delete($filename)) {
            throw new \Exception("Error deleting file {$filename}");
        }
    }

    public function errorIfNotExists(string $filename): void
    {
        if (!File::exists($filename)) {
            throw new \Exception("Error file {$filename} does not exists");
        }
    }
}
