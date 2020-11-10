<?php

namespace Sculptor\Agent\Contracts;

use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface Action
{
    public function error(): ?string;

    public function inserted(): ?Queue;
}
