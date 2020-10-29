<?php

namespace Sculptor\Agent\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Contracts\QueueRepository as QueueRepositoryInterface;
use Sculptor\Agent\Enums\QueueStatusType;
use Sculptor\Agent\Repositories\Entities\Queue;

/**
 * Class QueueRepository.
 *
 * @package namespace Sculptor\Agent\Repositories;
 */
class QueueRepository extends BaseRepository implements QueueRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Queue::class;
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
     * @param string $type
     * @return Queue
     * @throws ValidatorException
     */
    public function insert(string $type = 'unknown'): Queue
    {
        return $this->create([ 'uuid' => Str::uuid(), 'type' => $type, 'status' => QueueStatusType::WAITING ]);
    }

    public function clean(): int
    {
        $older = Carbon::now()->subDays( 30 );

        return Queue::where( 'created_at', '<=', $older )->delete();
    }
}
