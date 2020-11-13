<?php

namespace Sculptor\Agent\Monitors;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Illuminate\Support\Str;
use Sculptor\Foundation\Support\Replacer;

class Context
{
    /**
     * @var string
     */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function parse(array $context): array
    {
        $replaced = Replacer::make($this->message);

        foreach ($context as $key => $value) {
            $replaced->replace('{' . $key . '}', $value)
                ->replace('{' . Str::upper($key) . '}', $value);
        }

        return array_merge($context, ['message' => $replaced->value($context)]);
    }
}
