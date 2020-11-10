<?php

namespace Sculptor\Agent\Jobs\Domains\Support;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Configuration;
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

    /**
     * @param string $from
     * @param string $identity
     * @param array $command
     * @param int|null $timeout
     * @param callable|null $realtime
     * @throws Exception
     */
    public function runAs(
        string $from,
        string $identity,
        array $command,
        int $timeout = null,
        callable $realtime = null
    ): void {
        $command = array_merge(['sudo', '-u', $identity], $command);

        $this->run($from, $command, $timeout, $realtime);
    }

    /**
     * @param string $from
     * @param array $command
     * @param int|null $timeout
     * @param callable|null $realtime
     * @throws Exception
     */
    public function run(string $from, array $command, int $timeout = null, callable $realtime = null): void
    {
        $runner = $this->runner
            ->timeout($timeout)
            ->from($from);

        if ($realtime == null) {
            $runner->runOrFail($command);

            return;
        }

        $result = $runner->realtime($command, $realtime);

        if (!$result->success()) {
            throw new Exception($result->error());
        }
    }

    public function deleteIfExists(string $filename): void
    {
        if (!File::exists($filename)) {
            return;
        }

        if (!File::delete($filename)) {
            throw new Exception("Error deleting file {$filename}");
        }
    }

    public function errorIfNotExists(string $filename): void
    {
        if (!File::exists($filename)) {
            throw new Exception("Error file {$filename} does not exists");
        }
    }
}
