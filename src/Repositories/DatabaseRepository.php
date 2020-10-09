<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Repositories\Contracts\DatabaseRepository as DatabaseRepositoryInterface;
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

    public function byName(string $name): Database
    {
        $database = $this->findByField(['name' => $name]);

        if ($database->count() == 0) {
            throw new Exception("Cannot find database {$name}");
        }

        return $database->first();
    }

    public function exists(string $name): bool
    {
        $database = $this->findByField(['name' => $name]);

        return $database->count() > 0;
    }
}
