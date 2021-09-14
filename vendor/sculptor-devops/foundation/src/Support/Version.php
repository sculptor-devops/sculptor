<?php namespace Sculptor\Foundation\Support;


use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\EnvParser;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Version
{
    /**
     * @var string
     */
    private $filename = '/etc/os-release';
    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var EnvParser
     */
    private $parser;

    /**
     * Version constructor.
     * @param Runner $runner
     */
    public function __construct(Runner $runner)
    {
        $this->parser = new EnvParser($this->filename);

        $this->runner = $runner;
    }

    /**
     * @return string|null
     */
    public function version(): ?string
    {
        return $this->parser->get('VERSION_ID');
    }

    /**
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->parser->get('VERSION');
    }

    /**
     * @param array<string> $versions
     * @param array<string> $architectures
     * @return bool
     */
    public function compatible(array $versions, array $architectures): bool
    {
        return in_array($this->version(), $versions) &&
               in_array($this->arch(), $architectures);
    }

    /**
     * @return string
     */
    public function arch(): string
    {
        return clearNewLine($this->runner->run(['uname', '-m'])->output());
    }

    /**
     * @return string
     */
    public function bits(): string
    {
        return clearNewLine($this->runner->run(['getconf', 'LONG_BIT'])->output());
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return "Version {$this->name()} ({$this->arch()})";
    }
}
