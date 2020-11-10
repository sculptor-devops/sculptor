<?php

namespace Sculptor\Agent\Jobs\Domains\Support;

use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Run
{
    /**
     * @var Run
     */
    private $runner;

    public function __construct(Run $runner)
    {
        $this->runner = $runner;
    }
}
