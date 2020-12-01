<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Str;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Parser
{
    public const CONTEXT_PATTERN = '~\{(?:[^{}]|(?R))*\}~';

    /**
     * @var LaravelLogViewer
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new LaravelLogViewer;
    }

    public function all(string $file): array
    {
        $this->parser->setFile($file);

        $parsed =  $this->parser->all();

        foreach ($parsed as &$line) {
            if (preg_match(Parser::CONTEXT_PATTERN, $line['text'], $match) > 0) {
                $line['text'] = Str::of($line['text'])->replace($match[0], '')->trim() . '';

                $line['payload'] = $match;
            }
        }

        return $parsed;
    }
    
    public function files(): array
    {
        return $this->parser->getFolderFiles();
    }
}
