<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Contracts\BackupRepository as BackupRepositoryInterface;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class BackupRepository extends BaseRepository implements BackupRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Backup::class;
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
     * @param int $id
     * @return Backup
     * @throws Exception
     */
    public function byId(int $id): Backup
    {
        $backup = $this->find($id);

        if ($backup == null) {
            throw new Exception("Backup {$id} not found");
        }

        return $backup;
    }
}
