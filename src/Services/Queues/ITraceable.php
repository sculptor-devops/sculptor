<?php

namespace Sculptor\Agent\Services\Queues;

use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface ITraceable
{
    /**
     * @var Queue
     */
    public ref(Queue $value = null): Queue;

    /**
     * @throws Exception
     */
    public function running(): void;

    /**
     * @throws Exception
     */
    public function finished(): void;


    /**
     * @throws Exception
     */
    public function do(): void;

    /**
     * @param string $error
     * @throws Exception
     */
    public function error(string $error): void;
}
