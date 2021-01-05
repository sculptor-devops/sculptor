<?php

namespace Sculptor\Agent\Repositories\Criteria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Contracts\CriteriaInterface;
use Sculptor\Agent\Configuration;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class OlderRecords implements CriteriaInterface
{
    /**
     * @param Model $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('created_at', '<',  Carbon::now()->subDays(60));

        return $model;
    }
}
