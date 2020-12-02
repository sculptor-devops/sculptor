<?php

namespace Sculptor\Agent\Logs;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Parser
{
    /**
     *
     */
    public const CONTEXT_PATTERN = '~\{(?:[^{}]|(?R))*\}~';

    /**
     * @var LaravelLogViewer
     */
    private $parser;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->parser = new LaravelLogViewer;
    }

    /**
     * @param string $file
     * @return array
     * @throws Exception
     */
    private function rows(string $file): array
    {
        $this->parser->setFile($file);

        $parsed = $this->parser->all();

        foreach ($parsed as &$line) {
            $payload = null;

            if (preg_match(Parser::CONTEXT_PATTERN, $line['text'], $match) > 0) {

                $line['text'] = Str::of($line['text'])->replace($match[0], '')->trim() . '';

                $payload = $match;
            }

            $line['payload'] = $payload;
        }

        return $parsed;
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function files(): Collection
    {
        return collect($this->parser->getFolderFiles());
    }

    /**
     * @param string|null $file
     * @return Collection
     * @throws Exception
     */
    public function file(string $file = null): Collection
    {
        if ($file == null) {
            $file = $this->files()->first();
        }

        return collect($this->rows($file))
            ->map(function ($row) {
                return [
                    'level' => $row['level'],
                    'date' => Carbon::parse($row['date']),
                    'text' => $row['text'],
                    'stack' => $row['stack'],
                    'context' => $row['payload']
                ];
            });
    }
}
