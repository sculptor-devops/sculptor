<?php

namespace Sculptor\Agent\Logs\Support;

use Illuminate\Support\Collection;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class LogContext
{
    protected $name = '';

    /**
     * @var Collection
     */
    private $context;

    /**
     * LogIpContext constructor.
     * @param array|null $context
     */
    public function __construct(array $context = null)
    {
        $this->context = collect($context ?? [])->map(function ($item) {
            return json_decode($item, true);
        });
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->context->map(function ($item) {
            if ($item == null) {
                return null;
            }

            if (array_key_exists($this->name, $item)) {
                return $item[$this->name];
            }

            return null;
        })->toArray();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return collect($this->toArray())->join(', ');
    }
}
