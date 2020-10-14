<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Exceptions\ActionJobRunException;
use Sculptor\Agent\Contracts\ITraceable;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Queues\Queues;

class Base
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
     * Base constructor.
     * @param Queues $queues
     */
    public function __construct(Queues $queues)
    {
        $this->queues = $queues;
    }

    /**
     * @param ITraceable $job
     * @return bool
     * @throws Exception
     */
    protected function run(ITraceable $job): bool
    {
        $result = $this->queues->await($job, 'system');

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

    protected function report(string $message): void
    {
        Logs::actions()->error($message);

        $this->error = $message;
    }
}
