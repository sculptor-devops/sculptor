<?php

namespace Sculptor\Agent\Support;

use Sculptor\Agent\Exceptions\InvalidNumberOfParameters;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Exception;

class Parametrizer
{
    public const SEPARATOR = '::';

    /**
     * @var string
     */
    private $parameters;
    /**
     * @var int
     */
    private $expected;

    /**
     * @param string $parameters
     * @param int $expected
     * @throws Exception
     */
    public function __construct(string $parameters, int $expected = 2)
    {
        $this->parameters = $parameters;

        $this->expected = $expected;

        if (count($this->tokens()) != $this->expected) {
            throw new InvalidNumberOfParameters($this->expected());
        }
    }

    /**
     * @return int
     */
    public function expected(): int
    {
        return $this->expected;
    }

    /**
     * @return array
     */
    public function tokens(): array
    {
        return explode(Parametrizer::SEPARATOR, $this->parameters);
    }

    /**
     * @param int $index
     * @return string|null
     */
    public function token(int $index): ?string
    {
        $tokens = $this->tokens();

        if (count($tokens) < ($index + 1)) {
            return null;
        }

        return $tokens[$index];
    }

    /**
     * @return string
     */
    public function first(): string
    {
        return $this->token(0);
    }

    /**
     * @return string
     */
    public function last(): string
    {
        return $this->token($this->expected - 1);
    }
}
