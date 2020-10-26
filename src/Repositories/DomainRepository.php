<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Enums\DomainType;
use Sculptor\Agent\Exceptions\DomainNotFound;
use Sculptor\Agent\PasswordGenerator;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Contracts\DomainRepository as DomainRepositoryInterface;

class DomainRepository extends BaseRepository implements DomainRepositoryInterface
{
    /**
     * @var PasswordGenerator
     */
    private $password;

    public function __construct(Application $app, PasswordGenerator $password)
    {
        parent::__construct($app);

        $this->password = $password;
    }

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
     * @param string $hash
     * @return Domain
     * @throws DomainNotFound
     */
    public function byHash(string $hash): Domain
    {
        foreach ($this->all() as $domain) {
            if ($domain->externalId() == $hash) {
                return $domain;
            }
        }

        throw new DomainNotFound($hash);
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
                    'name' => $name,
                    'type' => $type,
                    'certificate' => CertificatesTypes::SELF_SIGNED,
                    'user' => SITES_USER,
                    'status' => DomainStatusType::NEW,
                    'vcs' => 'https://github.com/laravel/laravel.git',
                    'deployer' => SITES_DEPLOY,
                    'install' => SITES_INSTALL,
                    'token' => $this->password->token()
                ]);

            case DomainType::GENERIC:
                return $this->create([
                    'name' => $name,
                    'type' => $type,
                    'certificate' => CertificatesTypes::SELF_SIGNED,
                    'user' => SITES_USER,
                    'status' => DomainStatusType::NEW,
                    'vcs' => 'https://github.com/username/respository.git',
                    'deployer' => 'deploy',
                    'install' => 'deploy:install',
                    'token' => $this->password->token()
                ]);
        }

        throw new Exception("Invalid domain type {$type}");
    }
}
