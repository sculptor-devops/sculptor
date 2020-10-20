<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Enums\DomainType;
use Sculptor\Agent\Exceptions\DomainNotFound;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Contracts\DomainRepository as DomainRepositoryInterface;

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
        $domains = $this->findByField(['name' => $name]);

        if ($domains->count() == 0) {
            throw new DomainNotFound($name);
        }

        return $domains->first();
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

    /**
     * @param string $name
     * @param string $type
     * @return Domain
     * @throws Exception
     */
    public function factory(string $name, string $type): Domain
    {
        $domains = $this->findByField(['name' => $name]);

        if ($domains->count() > 0) {
            return $domains->first();
        }

        switch ($type) {
            case DomainType::LARAVEL:
                return $this->create([
                    'type' => $type,
                    'certificate' => CertificatesTypes::SELF_SIGNED,
                    'user' => SITES_USER,
                    'status' => DomainStatusType::NEW,
                    'vcs' => 'https://github.com/laravel/laravel.git',
                    'deployer' => SITES_DEPLOY,
                    'install' => SITES_INSTALL
                ]);

            case DomainType::GENERIC:
                return $this->create([
                    'type' => $type,
                    'certificate' => CertificatesTypes::SELF_SIGNED,
                    'user' => SITES_USER,
                    'status' => DomainStatusType::NEW,
                    'vcs' => 'https://github.com/username/respository.git',
                    'deployer' => 'deploy',
                    'install' => 'deploy:install'
                ]);
        }

        throw new Exception("Invalid domain type {$type}");
    }
}
