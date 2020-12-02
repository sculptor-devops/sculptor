<?php

namespace Sculptor\Agent\Logs;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Illuminate\Support\Collection;

class LogContext
{
    /**
     * @var Collection
     */
    private $context;

    /**
     * LogContext constructor.
     * @param array|null $context
     */
    public function __construct(array $context = null)
    {
        $this->context = collect($context ?? [])->map(function ($item) {
            return json_decode($item, true);
        });

        $this->context->__toString()
    }

    /**
     * @return string
     */
    public function ip(): string
    {
        return $this->context->map(function ($item) {
            return $item['ip'];
        })->join(', ');
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return $this->context->map(function ($item) {
            return $item['tag'];
        })->join(',');
    }
}
