<?php

namespace Sculptor\Agent\Queues;

use Exception;
use ReflectionClass;
use Illuminate\Support\Facades\Queue as Dispatcher;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Exceptions\QueueJobCreateException;
use Sculptor\Agent\Exceptions\QueueJobNotTraceableException;
use Sculptor\Agent\Exceptions\QueueJobTimeoutException;
use Sculptor\Agent\Repositories\QueueRepository;
use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Queues
{
    /**
     * @var QueueRepository
     */
    private $repository;

    /**
     * Queues constructor.
     * @param QueueRepository $repository
     */
    public function __construct(QueueRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ITraceable $job
     * @return bool
     * @throws Exception
     */
    private function traceable(ITraceable $job): bool
    {
        $uses = array_keys((new ReflectionClass($job))->getTraits());

        return in_array(Traceable::class, $uses);
    }

    /**
     * @param ITraceable $job
     * @param string $queue
     * @return Queue
     * @throws QueueJobNotTraceableException
     * @throws Exception
     */
    public function insert(ITraceable $job, string $queue = 'events'): Queue
    {
        if (!$this->traceable($job)) {
            throw new QueueJobNotTraceableException();
        }

        $job->ref($this->repository->insert(get_class($job)));

        Dispatcher::pushOn($queue, $job);

        return $job->ref();
    }

    /**
     * @param string $uuid
     * @return Queue
     */
    public function find(string $uuid): Queue
    {
        return $this->repository->findByField('uuid', $uuid)->first();
    }

    /**
     * @param ITraceable $job
     * @param string $queue
     * @param float|int $timeout
     * @return Queue
     * @throws QueueJobCreateException
     * @throws QueueJobTimeoutException
     * @throws QueueJobNotTraceableException
     */
    public function await(ITraceable $job, string $queue = 'events', int $timeout = QUEUE_TASK_TIMEOUT): Queue
    {
        $waited = 0;

        $entity = $this->insert($job, $queue);

        if (!$entity) {
            throw new QueueJobCreateException();
        }

        while (true) {
            $entity = $this->find($entity->uuid);

            if ($entity->finished() || $entity->error()) {
                break;
            }

            usleep(QUEUE_TASK_ROUND_TRIP);

            $waited += QUEUE_TASK_ROUND_TRIP;

            if ($waited > $timeout) {
                throw new QueueJobTimeoutException();
            }
        }

        return $entity;
    }
}
