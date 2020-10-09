<?php

namespace Sculptor\Agent\Actions;

use Exception;
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
     */
    protected function run(ITraceable $job): bool
    {
        try {
            $result = $this->queues->await($job, 'system');

            if ($result->ok()) {
                return true;
            }

            $this->error = $result->error;

            return false;
        } catch (Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }
}
