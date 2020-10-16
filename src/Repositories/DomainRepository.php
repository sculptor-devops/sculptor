<?php


namespace Sculptor\Agent\Repositories;


use Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Repositories\Entities\Domain;
use \Sculptor\Agent\Contracts\DomainRepository as DomainRepositoryInterface;

class DomainRepository extends BaseRepository implements DomainRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Domain::class;
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
     * @param string $name
     * @return Domain
     * @throws Exception
     */
    public function byName(string $name): Domain
    {
        $database = $this->findByField(['name' => $name]);

        if ($database->count() == 0) {
            throw new Exception($name);
        }

        return $database->first();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        $domain = $this->findByField(['name' => $name]);

        return $domain->count() > 0;
    }
}
