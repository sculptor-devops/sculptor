<?php

namespace Sculptor\Agent\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Contracts\AlarmRepository as AlarmRepositoryInterface;
use Sculptor\Agent\Repositories\Entities\Alarm;

class AlarmRepository extends BaseRepository implements AlarmRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Alarm::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function byId(int $id): Alarm
    {
        $domains = $this->find($id);

        if ($domains->count() == 0) {
            throw new \Exception("Cannot find monitor {$id}");
        }

        return $domains->first();
    }
}
