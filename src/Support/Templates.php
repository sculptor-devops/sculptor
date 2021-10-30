<?php

namespace Sculptor\Agent\Support;

use Sculptor\Foundation\Support\Template;
use Illuminate\Support\Facades\File;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Templates
{
    private $locations = [];

    public function __construct()
    {
        $this->locations = [ $this->system() ];

        if (File::exists($this->user())) {
            $this->locations[] = $this->user();
        }
    }

    private function user(): string
    {
        return userhome() . '.config/sculptor/templates';
    }

    private function directories(string $basepath): array
    {
        $result = [];

        foreach (File::directories($basepath) as $directory) {
            $result[basename($directory)] = TemplateData::from($directory);
        }

        return $result;
    }

    private function system(): string
    {
        return  base_path('templates');
    }

    public function domains(): array
    {
        $domains = [];

        foreach ($this->locations as $path) {
            $domains = array_merge($domains, $this->directories($path));
        }

        return $domains;
    }
}
