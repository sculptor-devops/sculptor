<?php
namespace Scultor\Agent;

use Exception;
use Sculptor\Agent\Jobs\DatabaseCreate;
use Sculptor\Agent\Jobs\DatabaseDelete;
use Sculptor\Agent\Queues\ITraceable;
use Sculptor\Agent\Queues\Queues;

class Actions
{
    /**
     * @var Queues
     */
    private $queues;

    /**
     * @var
     */
    private $error;

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
    private function run(ITraceable $job): bool
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
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function createDatabase(string $name): bool
    {
        return $this->run(new DatabaseCreate($name));
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function deleteDatabase(string $name): bool
    {
        return $this->run(new DatabaseDelete($name));
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }
}
