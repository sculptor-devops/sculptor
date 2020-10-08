<?php

namespace Sculptor\Agent\Services\Queues;

use DB;
use Exception;
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
     * @var Queue
     */
    public function ref(Queue $value = null): Queue
    {
	if($value != null) {
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

        $this->changeStatus(QUEUE_STATUS_RUNNING);
    }

    /**
     * @throws Exception
     */
    public function finished(): void
    {
        if ($this->transaction) {
	    DB::commit();
        }

        $this->changeStatus(QUEUE_STATUS_OK);
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

        $this->changeStatus(QUEUE_STATUS_ERROR, $error);
    }

    public function handle()
    {
        if ($this->ref == null) {
           throw new Exception('Queue ref is null');
        }

        try {
            $this->running();

            $this->do(); 

            $this->finished();

        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param string $status
     * @param string|null $error
     * @throws Exception
     */
    private function changeStatus(string $status, string $error = null): void
    {
        if ($this->ref === null) {
            throw new Exception('Queue ref is null');
        }

        $this->ref->update(['status' => $status, 'error' => $error]);
    }
}
