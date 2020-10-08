<?php

namespace Sculptor\Agent\Queues;

use Exception;
use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface ITraceable
{
    /**
     * @return Queue
     * @var Queue
     */
    public function ref(Queue $value = null): Queue;

    /**
     * @throws Exception
     */
    public function running(): void;

    /**
     * @throws Exception
     */
    public function finished(): void;

    /**
     * @param string $error
     * @throws Exception
     */
    public function error(string $error): void;
}
