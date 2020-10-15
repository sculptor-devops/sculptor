<?php

namespace Sculptor\Agent\Contracts;

use Sculptor\Agent\Repositories\Entities\Domain;

interface DomainAction
{
    public function run(Domain $domain): bool;
}
