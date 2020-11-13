<?php

namespace Sculptor\Agent\Monitors\Alarms;

use Exception;
use Illuminate\Support\Facades\Http;
use Sculptor\Agent\Contracts\Alarm;
use Sculptor\Agent\Monitors\Context;
use Sculptor\Agent\Monitors\Parametrizer;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Bash implements Alarm
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param string $to
     * @param string $message
     * @param array $context
     * @throws Exception
     */
    public function emit(string $to, string $message, array $context): void
    {
        $replaced = new Context($message);

        $this->runner
            ->env($replaced->parse($context))
            ->runOrFail(explode(' ', $to));
    }
}
