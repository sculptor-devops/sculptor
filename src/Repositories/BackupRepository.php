<?php

namespace Sculptor\Agent\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Contracts\BackupRepository as BackupRepositoryInterface;

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
}
