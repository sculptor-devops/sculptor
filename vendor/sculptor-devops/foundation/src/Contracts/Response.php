<?php namespace Sculptor\Foundation\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Response
{
    /**
     * @return bool
     */
    public function success(): bool;

    /**
     * @return string
     */
    public function output(): string;

    /**
     * @return string
     */
    public function error(): string;

    /**
     * @return int|null
     */
    public function code(): ?int;
}
