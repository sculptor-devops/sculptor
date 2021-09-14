<?php namespace Sculptor\Foundation\Services;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Daemons extends BaseService
{
    /**
     * @var string
     */
    private $command = 'systemctl';

    /**
     * @param string $name
     * @return bool
     */
    public function active(string $name): bool
    {
        $result = $this->service($this->command, "is-active", $name);

        return 'active' == clearNewLine($result->output());
    }

    /**
     * @param string $name
     * @return bool
     */
    public function reload(string $name): bool
    {
        return $this->report($this->service($this->command, "reload", $name))
            ->success();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function restart(string $name): bool
    {
        return $this->report($this->service($this->command, "restart", $name))
            ->success();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function start(string $name): bool
    {
        return $this->report($this->service($this->command, "start", $name))
            ->success();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function stop(string $name): bool
    {
        return $this->report($this->service($this->command, "stop", $name))
            ->success();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function enable(string $name): bool
    {
        return $this->report($this->service($this->command, "enable", $name))
            ->success();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function disable(string $name): bool
    {
        return $this->report($this->service($this->command, "disable", $name))
            ->success();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function installed(string $name): bool
    {
        return $this->report($this->runner->run(['dpkg', '-s', $name]))
            ->success();
    }
}
