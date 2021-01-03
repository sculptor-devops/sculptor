<?php

namespace Sculptor\Agent\Jobs\Domains\Support;

use Exception;
use Illuminate\Support\Facades\File;
use Sculptor\Agent\Configuration;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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
    ): void
    {
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

    /**
     * @param string $filename
     * @throws Exception
     */
    public function deleteIfExists(string $filename): void
    {
        if (!File::exists($filename)) {
            return;
        }

        if (!File::delete($filename)) {
            throw new Exception("Error deleting file {$filename}");
        }
    }

    /**
     * @param string $filename
     * @throws Exception
     */
    public function errorIfNotExists(string $filename): void
    {
        if (!File::exists($filename)) {
            throw new Exception("Error file {$filename} does not exists");
        }
    }

    /**
     * @param string $filename
     * @param string $content
     * @param string $identity
     * @throws Exception
     */
    public function saveAs(string $filename, string $content, string $identity): void
    {
        if (!File::put($filename, $content)) {
            throw new Exception("Error writing file {$filename}");

        }

        $this->chown($filename, $identity);
    }

    /**
     * @param string $filename
     * @param string $identity
     * @throws Exception
     */
    public function chown(string $filename, string $identity): void
    {
        $this->run(File::dirname($filename), ['chown', "{$identity}:{$identity}", $filename]);
    }
}
