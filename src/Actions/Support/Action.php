<?php

namespace Sculptor\Agent\Actions\Support;

use Exception;
use Sculptor\Agent\Exceptions\ActionJobRunException;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Exceptions\QueueJobCreateException;
use Sculptor\Agent\Exceptions\QueueJobNotTraceableException;
use Sculptor\Agent\Exceptions\QueueJobTimeoutException;
use Sculptor\Agent\Facades\Logs;
use Sculptor\Agent\Queues\Queues;
use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Action
{
    /**
     * @var Queues
     */
    protected $queues;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var Queue|null
     */
    protected $inserted;

    /**
     * Action constructor.
     * @param Queues $queues
     */
    public function __construct(Queues $queues)
    {
        $this->queues = $queues;
    }

    /**
     * @param ITraceable $job
     * @param int $timeout
     * @return bool
     * @throws ActionJobRunException
     * @throws QueueJobCreateException
     * @throws QueueJobNotTraceableException
     * @throws QueueJobTimeoutException
     */
    public function run(ITraceable $job, int $timeout = QUEUE_TASK_TIMEOUT): bool
    {
        $this->inserted = $this->queues
            ->await($job, 'system', $timeout);

        if ($this->inserted->ok()) {
            return true;
        }

        throw new ActionJobRunException($this->inserted->error);
    }

    /**
     * @param ITraceable $job
     * @return bool
     * @throws ActionJobRunException
     * @throws QueueJobCreateException
     * @throws QueueJobNotTraceableException
     * @throws QueueJobTimeoutException
     */
    public function runIndefinite(ITraceable $job): bool
    {
        $this->inserted = $this->queues->await($job, 'system', QUEUE_TASK_NO_TIMEOUT);

        if ($this->inserted->ok()) {
            return true;
        }

        throw new ActionJobRunException($this->inserted->error);
    }

    /**
     * @param ITraceable $job
     * @return bool
     * @throws QueueJobNotTraceableException
     */
    public function runAndExit(ITraceable $job): bool
    {
        $this->inserted = $this->queues->insert($job, 'system');

        return true;
    }

    /**
     * @return Queue|null
     */
    public function inserted(): ?Queue
    {
        return $this->inserted;
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function report(string $message): bool
    {
        Logs::actions()->error($message);

        $this->error = $message;

        return false;
    }

    /**
     * @param string $description
     * @param ITraceable $job
     * @return bool
     */
    public function job(string $description, ITraceable $job): bool
    {
        Logs::actions()->info($description);

        try {
            $this->run($job);
        } catch (Exception $e) {
            $this->report("{$description}: {$e->getMessage()}");

            return false;
        }

        return true;
    }
}
