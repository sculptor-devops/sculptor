<?php

namespace Sculptor\Agent\Repositories;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Sculptor\Agent\Repositories\Contracts\QueueRepository as QueueRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Queue;
use Sculptor\Agent\Validators\QueueValidator;

/**
 * Class QueueRepository.
 *
 * @package namespace Sculptor\Agent\Repositories;
 */
class QueueRepository extends BaseRepository implements QueueRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Queue::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    public function insert()
    {
	return $this->create( [ 'uuid' => Str::uuid(), 'status' => QUEUE_STATUS_WAITING ] );
    } 
}
