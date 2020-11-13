<?php

namespace Sculptor\Agent\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Contracts\MonitorRepository as MonitorRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Monitor;

class MonitorRepository extends BaseRepository implements MonitorRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Monitor::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function byId(int $id): Monitor
    {
        $domains = $this->find($id);

        if ($domains->count() == 0) {
            throw new \Exception("Cannot find monitor {$id}");
        }

        return $domains->first();
    }
}
