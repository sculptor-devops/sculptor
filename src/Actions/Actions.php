<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Exceptions\ActionJobRunException;
use Sculptor\Agent\Queues\ITraceable;
use Sculptor\Agent\Queues\Queues;

class Actions
{
    /**
     * @var Queues
     */
    protected $queues;

    /**
     * @var
     */
    protected $error;

    /**
     * Actions constructor.
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
}
