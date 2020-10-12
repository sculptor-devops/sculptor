<?php

namespace Sculptor\Agent\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Contracts\QueueRepository as QueueRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Queue;

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
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param string $type
     * @return LengthAwarePaginator|Collection|mixed
     * @throws ValidatorException
     */
    public function insert(string $type = 'unknown')
    {
        return $this->create([ 'uuid' => Str::uuid(), 'type' => $type, 'status' => QUEUE_STATUS_WAITING ]);
    }
}
