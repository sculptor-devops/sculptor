<?php

namespace Sculptor\Agent\Services\Queues;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Queue as Dispatcher;
use Sculptor\Agent\Repositories\QueueRepository;
use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Manager
{
    /**
     * @var QueueRepository
     */
    private $repository;

    public function __construct(QueueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function insert(object $job, string $queue = 'events'): Queue
    {
        $job->ref = $this->repository->insert();

        Dispatcher::pushOn($queue, $job);

        return $job->ref;
    }

    public function find(string $uuid): Queue
    {
        return $this->repository->findByField('uuid', $uuid)->first();
    }

    public function await(object $job, string $queue = 'events'): Queue
    {
        $waited = 0;

        $entity = $this->insert($job, $queue);

        if (!$entity) {
            throw new Exception('Cannot create job');
        }

        while(true) {
            $entity = $this->find($entity->uuid);

            if ($entity->finished()) {
                break;
            }

            usleep(QUEUE_TASK_ROUND_TRIP);

            $waited += QUEUE_TASK_ROUND_TRIP;

            if ($waited > QUEUE_TASK_TIMEOUT) {
                throw new Exception('Task Timeout');
            }
        }

        return $entity;
    }
}
