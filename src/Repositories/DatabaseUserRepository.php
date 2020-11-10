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

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DatabaseUserRepository extends BaseRepository implements QueueRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return DatabaseUser::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot(): void
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
        $user = $database->users
            ->where('name', $name)
            ->first();

        if ($user == null) {
            throw new DatabaseUserNotFoundException("Cannot find user {$name}");
        }

        return $user;
    }
}
