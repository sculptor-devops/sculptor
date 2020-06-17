<?php

namespace Sculptor\Agent\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Sculptor\Agent\Repositories\Contracts\DatabaseRepository as DatabaseRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Validators\DatabaseValidator;

/**
 * Class DatabaseRepository.
 *
 * @package namespace Sculptor\Agent\Repositories;
 */
class DatabaseRepository extends BaseRepository implements DatabaseRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Database::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
