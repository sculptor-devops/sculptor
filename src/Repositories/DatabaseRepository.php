<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Exceptions\DatabaseNotFoundException;
use Sculptor\Agent\Contracts\DatabaseRepository as DatabaseRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Database;

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
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param string $name
     * @return Database
     * @throws DatabaseNotFoundException
     */
    public function byName(string $name): Database
    {
        $database = $this->findByField(['name' => $name]);

        if ($database->count() == 0) {
            throw new DatabaseNotFoundException($name);
        }

        return $database->first();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        $database = $this->findByField(['name' => $name]);

        return $database->count() > 0;
    }
}
