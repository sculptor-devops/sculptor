<?php

namespace Sculptor\Agent\Repositories\Criteria;

use Carbon\Carbon;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Contracts\CriteriaInterface;
use Sculptor\Agent\Configuration;

class OlderRecords implements CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('created_at', '<',  Carbon::now()->subDays(14));

        return $model;
    }
}
