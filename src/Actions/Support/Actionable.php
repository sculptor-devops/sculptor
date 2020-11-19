<?php

namespace Sculptor\Agent\Actions\Support;

use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

trait Actionable
{
    /**
     * @var Action
     */
    private $action;

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->action->error();
    }

    public function inserted(): ?Queue
    {
        return $this->action->inserted();
    }
}
