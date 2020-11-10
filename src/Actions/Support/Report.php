<?php

namespace Sculptor\Agent\Actions\Support;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

trait Report
{
    /**
     * @var Action|null
     */
    private $action;

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->action->error();
    }
}
