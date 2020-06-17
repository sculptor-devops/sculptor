<?php

namespace Sculptor\Agent\Services\Queues;

use Scultor\Agent\Repositories\Entity\Queue;

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


    private function finished()
    {
	 if ($this->ref === null) {
             throw new \Exception('Queue ref is null');
	 }

         $this->ref->update(['status' => QUEUE_STATUS_OK]);
    }


    private function error(string $error)
    {
         if ($this->ref === null) {
             throw new \Exception('Queue ref is null');
         }

         $this->ref->update(['status' => QUEUE_STATUS_ERROR, 'error' => $error]);
    }

}
