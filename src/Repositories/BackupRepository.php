<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Enums\BackupType;
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
        $backup = $this->findByField(['id' => $id]);

        if ($backup->count() != 1) {
            throw new Exception("Backup {$id} not found");
        }

        return $backup->first();
    }

    /**
     * @param string $type
     * @return Backup
     * @throws ValidatorException
     * @throws Exception
     */
    public function make(string $type): Backup
    {
        if (!BackupType::has($type)) {
            throw new Exception("Unknown backup type {$type}");
        }

        return $this->create(['type' => $type]);
    }
}
