<?php

namespace Sculptor\Agent\Contracts;

use Sculptor\Agent\Repositories\Entities\Domain;

interface DomainAction
{
    public function compile(Domain $domain): bool;

    public function delete(Domain $domain): bool;
}
