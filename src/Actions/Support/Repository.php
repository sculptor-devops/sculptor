<?php

namespace Sculptor\Agent\Actions\Support;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;

trait Repository
{
    /**
     * @var BaseRepository|null
     */
    private $repository;

    public function show(): Collection
    {
        return $this->repository->all();
    }
}
