<?php

namespace Sculptor\Foundation\Support;

use Exception;
use Illuminate\Support\Facades\File;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Template
{
    private string $filename;

    private string $content;

    private bool $brackets = false;

    private string $default;

    private string $custom;

    public function __construct(string $default, string $custom)
    {
        $this->default = $default;

        $this->custom = $custom;
    }

    private function filename(): string
    {
        if (File::exists($this->customized())) {
            return $this->customized();
        }

        return $this->path();
    }

    public function from(string $filename): Template
    {
        $this->filename = $filename;

        $this->content = File::get($this->filename());

        return $this;
    }

    public function customized(): string
    {
        return $this->custom . "/{$this->filename}";
    }

    public function path(): string
    {
        return $this->default . "/{$this->filename}";
    }

    public function replace(string $key, string $value): Template
    {
        if ($this->brackets) {
            $key = '{' . $key . '}';
        }

        $this->content = str_replace($key, $value, $this->content);

        return $this;
    }

    public function replaces(array $keys): Template
    {
        foreach ($keys as $key => $value) {
            $this->replace($key, $value);
        }

        return $this;
    }

    public function brackets(bool $enabled = true): Template
    {
        $this->brackets = $enabled;

        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    /**
     * @throws Exception
     */
    public function save(string $content): Template
    {
        $path = Filesystem::dirname($this->customized());

        if (!Filesystem::exists($path)) {
            Filesystem::directory($path);
        }

        Filesystem::put($this->customized(), $content);

        $this->content = $content;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function restore(): Template
    {
        $content = Filesystem::get($this->path());

        $this->save($content);

        return $this;
    }

    public function content(string $content): Template
    {
        $this->content = $content;

        return $this;
    }
}
