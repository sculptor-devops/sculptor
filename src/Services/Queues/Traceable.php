<?php

namespace Sculptor\Agent\Services\Queues;

use Exception;
use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

trait Traceable
{
    /**
     * @var Queue
     */
    public $ref;

    /**
     * @throws Exception
     */
    protected function running()
    {
        $this->changeStatus(QUEUE_STATUS_RUNNING);
    }

    /**
     * @throws Exception
     */
    protected function finished()
    {
        $this->changeStatus(QUEUE_STATUS_OK);
    }

    /**
     * @param string $error
     * @throws Exception
     */
    protected function error(string $error)
    {
        $this->changeStatus(QUEUE_STATUS_ERROR, $error);
    }

    /**
     * @param string $status
     * @param string|null $error
     * @throws Exception
     */
    private function changeStatus(string $status, string $error = null): void
    {
        if ($this->ref === null) {
            throw new Exception('Queue ref is null');
        }

        $this->ref->update(['status' => $status, 'error' => $error]);
    }
}
