<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Exceptions\DatabaseUserNotFoundException;
use Sculptor\Agent\Contracts\QueueRepository as QueueRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Repositories\Entities\DatabaseUser;

class DatabaseUserRepository extends BaseRepository implements QueueRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DatabaseUser::class;
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
     * @param Database $database
     * @param string $name
     * @return DatabaseUser
     * @throws DatabaseUserNotFoundException
     */
    public function byName(Database $database, string $name): DatabaseUser
    {
        $user = $database->users->where('name', $name);

        if ($user->count() == 0) {
            throw new DatabaseUserNotFoundException("Cannot find user {$name}");
        }

        return $user->first();
    }
}
