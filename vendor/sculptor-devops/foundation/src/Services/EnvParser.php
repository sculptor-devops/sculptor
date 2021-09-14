<?php namespace Sculptor\Foundation\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Sculptor\Foundation\Support\Replacer;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class EnvParser
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var array<int, string>|false
     */
    private $content = [];

    /**
     * EnvParser constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $this->parse();
    }

    /**
     *
     */
    private function parse(): void
    {
        $content = File::get($this->filename);
        if ($content) {
            $this->content = splitNewLine($content);

            return;
        }
    }

    /**
     * @param string $key
     * @param bool $quoted
     * @return string|null
     */
    public function get(string $key, bool $quoted = true): ?string
    {
        if (!$this->content) {

            return null;
        }

        foreach ($this->content as $line) {
            if (!Str::startsWith($line, $key)) {
                continue;
            }

            $value = Str::after($line, '=');

            if ($quoted) {
                return quoteContent($value);
            }

            return $value;
        }

        return null;
    }

    public function set(string $key, string $value, bool $quoted = true): bool
    {
        $this->parse();

        if (!$this->content) {

            return false;
        }

        $old = $this->get($key, $quoted);

        $replace = "{$key}={$old}";

        $replaced = "{$key}={$value}";

        if ($quoted) {
            $replace = "{$key}=\"{$old}\"";

            $replaced = "{$key}=\"{$value}\"";
        }

        $content = File::get($this->filename);

        $content = Replacer::make($content)
            ->replace($replace, $replaced)
            ->value();

        if (!File::put($this->filename, $content)) {
            return false;
        }

        return true;
    }
}
