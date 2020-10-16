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
     * @param bool $wait
     * @param bool $indefinite
     * @return bool
     * @throws ActionJobRunException
     * @throws QueueJobCreateException
     * @throws QueueJobNotTraceableException
     * @throws QueueJobTimeoutException
     */
    public function run(ITraceable $job, bool $wait = true, bool $indefinite = false): bool
    {
        if (!$wait) {
            $this->queues->insert($job, 'system');

            return true;
        }

        $timeout = QUEUE_TASK_TIMEOUT;

        if ($indefinite) {
            $timeout = QUEUE_TASK_NO_TIMEOUT;
        }

        $result = $this->queues->await($job, 'system', $timeout);

        if ($result->ok()) {
            return true;
        }

        throw new ActionJobRunException($result->error);
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }

    public function report(string $message): void
    {
        Logs::actions()->error($message);

        $this->error = $message;
    }
}
