<?php

namespace Sculptor\Agent\Queues;

use Exception;
use Illuminate\Support\Facades\DB;
use Sculptor\Agent\Enums\QueueStatusType;
use Sculptor\Agent\Exceptions\QueueJobRefUndefinedException;
use Sculptor\Agent\Repositories\Entities\Queue;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

trait Traceable
{
    /**
     * @var Queue
     */
    protected $ref;


    /**
     * @var bool
     */
    protected $transaction = true;

    /**
     * @return Queue
     * @var Queue
     */
    public function ref(Queue $value = null): Queue
    {
        if ($value != null) {
            $this->ref = $value;
        }

        return $this->ref;
    }

    /**
     * @throws Exception
     */
    public function running(): void
    {
        if ($this->transaction) {
            DB::beginTransaction();
        }

        $this->changeStatus(QueueStatusType::RUNNING);
    }

    /**
     * @throws Exception
     */
    public function ok(): void
    {
        if ($this->transaction) {
            DB::commit();
        }

        $this->changeStatus(QueueStatusType::OK);
    }

    /**
     * @param string $error
     * @throws Exception
     */
    public function error(string $error): void
    {
        if ($this->transaction) {
            DB::rollBack();
        }

        $this->changeStatus(QueueStatusType::ERROR, $error);
    }

    /**
     * @param string $status
     * @param string|null $error
     * @throws Exception
     */
    private function changeStatus(string $status, string $error = null): void
    {
        if ($this->ref === null) {
            throw new QueueJobRefUndefinedException();
        }

        $this->ref->update(['status' => $status, 'error' => $error]);
    }
}
