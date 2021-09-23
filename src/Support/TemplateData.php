<?php

namespace Sculptor\Agent\Support;

use Illuminate\Support\Arr;
use Sculptor\Foundation\Support\Filesystem;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class TemplateData
{
    private $data;

    function __construct(array $data)
    {
        $this->data = $data;
    }

    public function name(): string
    {
        return Arr::get($this->data, 'name');
    }

    public function type(): string
    {
        return Arr::get($this->data, 'type');
    }

    public static function from(string $filename): TemplateData
    {
        $content = Filesystem::json("{$filename}/metadata.json");

        return new TemplateData($content);
    }
}
