<?php namespace Sculptor\Foundation\Support;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Replacer
{
    /**
     * @var string
     */
    private $string;

    /**
     * Replacer constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function replace(string $key, string $value): self
    {
        $this->string = str_replace($key, $value, $this->string);

        return $this;
    }

    /**
     * @param array $keys
     * @return $this
     */
    public function replaces(array $keys): self
    {
	foreach($keys as $key => $value) {
	    $this->replace($key, $value);
	}

	return $this;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->string;
    }

    /**
     * @param string $string
     * @return Replacer
     */
    public static function make(string $string): Replacer
    {
        return new Replacer($string);
    }
}
