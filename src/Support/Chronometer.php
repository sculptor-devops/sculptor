<?php

namespace Sculptor\Agent\Support;

use Illuminate\Support\Carbon;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Chronometer
{
    /**
     * @var Carbon
     */
    private $started;

    /**
     * Chronometer constructor.
     */
    public function __construct()
    {
        $this->started = now();
    }

    /**
     * @return Chronometer
     */
    public static function start(): Chronometer
    {
        return new Chronometer();
    }

    /**
     * @return string
     */
    public function stop(): string
    {
        return now()->longAbsoluteDiffForHumans($this->started);
    }
}
