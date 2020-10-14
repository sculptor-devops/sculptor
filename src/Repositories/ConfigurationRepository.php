<?php


namespace Sculptor\Agent\Repositories;


use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Repositories\Entities\Configuration;
use \Sculptor\Agent\Contracts\ConfigurationRepository as ConfigurationRepositoryInterface;

class ConfigurationRepository extends BaseRepository implements ConfigurationRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Configuration::class;
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
