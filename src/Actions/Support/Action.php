<?php

namespace Sculptor\Agent\Actions\Support;

use Exception;
use Sculptor\Agent\Exceptions\ActionJobRunException;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Exceptions\QueueJobCreateException;
use Sculptor\Agent\Exceptions\QueueJobNotTraceableException;
use Sculptor\Agent\Exceptions\QueueJobTimeoutException;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Queues;

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
        $result = $this->queues->await($job, 'system', $timeout);

        if ($result->ok()) {
            return true;
        }

        throw new ActionJobRunException($result->error);
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
        $result = $this->queues->await($job, 'system', QUEUE_TASK_NO_TIMEOUT);

        if ($result->ok()) {
            return true;
        }

        throw new ActionJobRunException($result->error);
    }

    /**
     * @param ITraceable $job
     * @return bool
     * @throws QueueJobNotTraceableException
     */
    public function runAndExit(ITraceable $job): bool
    {
        $this->queues->insert($job, 'system');

        return true;
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
     */
    public function report(string $message): void
    {
        Logs::actions()->error($message);

        $this->error = $message;
    }
}
