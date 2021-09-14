<?php namespace Sculptor\Foundation\Runner;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Sculptor\Foundation\Contracts\Runner as RunnerInterface;
use Sculptor\Foundation\Contracts\Response as ResponseInterface;
use Sculptor\Foundation\Exceptions\PathNotFoundException;
use Exception;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Runner implements RunnerInterface
{
    /**
     * @var string
     */
    private $line;

    /**
     * @var array<string, string>
     */
    private $env = [];

    /**
     * @var string
     */
    private $input = null;

    /**
     * @var bool
     */
    private $useTty = false;
    /**
     * @var string
     */
    private $path = null;
    /**
     * @var int|null
     */
    private $timeout = 60;

    /**
     * @return $this|RunnerInterface
     */
    public function tty(): RunnerInterface
    {
        $this->useTty = true;

        return $this;
    }

    /**
     * @param string $path
     * @return $this|RunnerInterface
     * @throws PathNotFoundException
     */
    public function from(string $path): RunnerInterface
    {
        $this->path = $path;

        if (!file_exists($path)) {
            throw new PathNotFoundException($path);
        }

        return $this;
    }

    /**
     * @param int|null $timeout
     * @return $this|RunnerInterface
     */
    public function timeout(?int $timeout): RunnerInterface
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param array<string, string> $export
     * @return $this|RunnerInterface
     */
    public function env(array $export): RunnerInterface
    {
        $this->env = $export;

        return $this;
    }

    /**
     * @param string $input
     * @return $this|RunnerInterface
     */
    public function input(string $input): RunnerInterface
    {
        $this->input = $input;

        return $this;
    }


    /**
     * @param array $command
     * @return Process
     */
    private function process(array $command): Process
    {
        $this->line = join(' ', $command);

        $process = new Process($command, $this->path);

        $process->setTimeout($this->timeout);

        $process->setEnv($this->env);

        if ($this->useTty) {
            $process->setTty(true);
        }

        if ($this->input) {
            $process->setInput($this->input);
        }

        return $process;
    }


    /**
     * @param array<int, int|string> $command
     * @return Response
     */
    public function run(array $command): ResponseInterface
    {
        $process = $this->process($command);

        try {
            $process->mustRun();

            return $this->response($process->isSuccessful(), $process);

        } catch (ProcessFailedException $exception) {

            return $this->response(false, $process);
        }
    }


    /**
     * @param array<int, int|string> $command
     * @return string
     */
    public function runOrFail(array $command): string
    {
	    $result = $this->run($command);

	    if (!$result->success()) {
		throw new Exception($result->error());
	    }

	    return $result->output();
    }

    /**
     * @param array $command
     * @param callable $retrun
     * @return ResponseInterface
     */
    public function realtime(array $command, callable $retrun): ResponseInterface
    {
        $process = $this->process($command);

        try {
            $process->run($retrun);

            $process->wait();

            return $this->response($process->isSuccessful(), $process);
        } catch (ProcessFailedException $exception) {

            return $this->response(false, $process);
        }
    }

    /**
     * @param bool $status
     * @param Process<object> $process
     * @return Response
     */
    private function response(bool $status, Process $process)
    {
        return new Response(
            $status,
            $process->getOutput(),
            $process->getExitCode(),
            $process->getErrorOutput()
        );
    }

    /**
     * @return string
     */
    public function line(): string
    {
        return $this->line;
    }
}
