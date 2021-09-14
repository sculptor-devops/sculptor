<?php namespace Sculptor\Foundation\Runner;

use Sculptor\Foundation\Contracts\Response as ResponseInterface;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Response implements ResponseInterface
{
    /**
     * @var bool
     */
    private $success;
    /**
     * @var string
     */
    private $error = 'Unknown error';
    /**
     * @var int|null
     */
    private $code;
    /**
     * @var string
     */
    private $output;

    /**
     * Response constructor.
     * @param bool $success
     * @param string $output
     * @param int $code
     * @param string $error
     */
    public function __construct(bool $success, string $output, ?int $code = 0, string $error = '')
    {
        $this->success = $success;
        $this->output = $output;
        $this->code = $code;
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function success(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * @return int|null
     */
    public function code(): ?int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function output(): string
    {
        return $this->output;
    }
}
